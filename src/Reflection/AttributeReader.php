<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use Psr\{
    Cache\CacheItemPoolInterface, Log\LoggerAwareTrait, Log\LoggerInterface
};
use ReflectionAttribute,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionException,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty,
    SplFileInfo,
    Throwable,
    ValueError,
    WeakMap;

class AttributeReader
{

    use LoggerAwareTrait;

    private ?CacheItemPoolInterface $cachePool = null;

    /**
     * Configure a new instance
     *
     * @param ?LoggerInterface $logger
     * @param ?CacheItemPoolInterface $cachepool
     * @return static
     */
    public static function create(LoggerInterface $logger = null, CacheItemPoolInterface $cachepool = null): static
    {
        $instance = new static;
        $instance->logger = $logger;
        $instance->cachePool = $cachepool;
        return $instance;
    }

    /**
     * Get attributes for a class
     *
     * @param string|object $className
     * @param int|AttributeType $type
     * @param string|array $attributeNames
     * @return array<string,object[]>
     * @throws ValueError
     */
    public function getClassAttributes(
            string|object $className,
            int|AttributeType $type = AttributeType::ATTRIBUTE_ALL,
            string|array $attributeNames = []
    ): array
    {

        /** @var AttributeType $target */
        $target = $type instanceof AttributeType ? $type : AttributeType::from($type);

        $attributeNames = is_array($attributeNames) ? $attributeNames : [$attributeNames];

        $targetInt = $target->value;

        $result = [];
        try {

            if ($target->is(AttributeType::ATTRIBUTE_FUNCTION) || $target->is(AttributeType::ATTRIBUTE_PARAMETER)) {
                throw new ValueError(sprintf('Invalid type %s::%s defined for %s()', AttributeType::class, $target->name, __METHOD__));
            }

            $reflectionClass = new ReflectionClass($className);

            if ($this->cachePool) {
                $mtime = (new SplFileInfo($reflectionClass->getFileName()))->getMTime();
                $item = $this->cachePool->getItem(md5(sprintf('%s%d%d', is_object($className) ? $className::class : $className, $targetInt, $mtime)));
                if ($item->isHit()) {
                    return $item->get();
                }
            }


            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_CLASS) {
                $result[AttributeType::ATTRIBUTE_CLASS()->name] = $this->filterResults($this->getReflectionClassAttributes($reflectionClass), ...$attributeNames);
            }
            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_CLASS_CONSTANT) {
                $result[AttributeType::ATTRIBUTE_CLASS_CONSTANT()->name] = [];
                /** @var ReflectionClassConstant $reflectionClassConstant */
                foreach ($reflectionClass->getReflectionConstants() as $reflectionClassConstant) {
                    $name = $reflectionClassConstant->getName();
                    $result[AttributeType::ATTRIBUTE_CLASS_CONSTANT()->name][$name] = $this->filterResults($this->getReflectionClassConstantAttributes($reflectionClassConstant), ...$attributeNames);
                }
            }
            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_PROPERTY) {
                $result[AttributeType::ATTRIBUTE_PROPERTY()->name] = [];
                /** @var ReflectionProperty $reflectionProperty */
                foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                    $name = $reflectionProperty->getName();
                    $result[AttributeType::ATTRIBUTE_PROPERTY()->name][$name] = $this->filterResults($this->getReflectionPropertyAttributes($reflectionProperty), ...$attributeNames);
                }
            }
            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_METHOD) {
                $result[AttributeType::ATTRIBUTE_METHOD()->name] = [];
                /** @var ReflectionMethod $reflectionMethod */
                foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                    $name = $reflectionMethod->getName();
                    $result[AttributeType::ATTRIBUTE_METHOD()->name][$name] = $this->filterResults($this->getReflectionMethodAttributes($reflectionMethod), ...$attributeNames);
                }
            }


            if (count($result) > 0 && isset($item)) $this->cachePool->save($item->set($result));
        } catch (\ValueError $error) {
            throw $error;
        } catch (Throwable $error) {
            $this->logger && $this->logger->warning(sprintf('Cannot get attributes for class %s', is_object($className) ? $className::class : $className), [$error]);
        }

        return $result;
    }

    public function getClassMethodAttributes(string|object $class, string $methodName): array
    {
        static $cache = [];
        $className = is_object($class) ? get_class($class) : $class;
        $cache[$className] = $cache[$className] ?? [];
        if (isset($cache[$className][$methodName])) return $cache[$className][$methodName];
        if ($this->cachePool) {
            $item = $this->cachePool->getItem(md5(sprintf('%s|%s::%s()', static::class, $className, $methodName)));
            if ($item->isHit()) return $cache[$className][$methodName] = $item->get();
        }
        $result = [];
        try {

            $result = $this->getReflectionMethodAttributes(new \ReflectionMethod($className, $methodName));
        } catch (\Throwable $error) {
            $this->logger && $this->logger->warning(sprintf('Cannot get class %s methods attributes.', $className), [$error]);
        }

        if ($this->cachePool && count($result) > 0) $this->cachePool->save($item->set($result));
        return $result;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return object[]
     */
    public function getReflectionClassAttributes(ReflectionClass $reflectionClass): array
    {
        return $this->getAttributeInstances($reflectionClass->getName(), ...$reflectionClass->getAttributes());
    }

    /**
     * @param ReflectionMethod $reflectionMethod
     * @return object[]
     */
    public function getReflectionMethodAttributes(ReflectionMethod $reflectionMethod): array
    {
        return $this->getAttributeInstances($reflectionMethod->getName(), ...$reflectionMethod->getAttributes());
    }

    /**
     * @param ReflectionProperty $reflectionProperty
     * @return object[]
     */
    public function getReflectionPropertyAttributes(ReflectionProperty $reflectionProperty): array
    {
        return $this->getAttributeInstances($reflectionProperty->getName(), ...$reflectionProperty->getAttributes());
    }

    /**
     * @param ReflectionClassConstant $reflectionClassConstant
     * @return object[]
     */
    public function getReflectionClassConstantAttributes(ReflectionClassConstant $reflectionClassConstant): array
    {
        return $this->getAttributeInstances($reflectionClassConstant->getName(), ...$reflectionClassConstant->getAttributes());
    }

    public function getReflectionFunctionAttributes(ReflectionFunction $reflectionFunction): array
    {
        return $this->getAttributeInstances($reflectionFunction->getName(), ...$reflectionFunction->getAttributes());
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @return object[]
     */
    public function getReflectionParameterAttributes(ReflectionParameter $reflectionParameter)
    {
        return $this->getAttributeInstances($reflectionParameter->getName(), ...$reflectionParameter->getAttributes());
    }

    /**
     * Get Metadata infos for attributes
     *
     * @param string $attributeName
     * @return AttributeMetadata
     */
    public function getAttributeMetadata(string $attributeName): AttributeMetadata
    {

        static $cache = [];
        $cache[$attributeName] = $cache[$attributeName] ?? new AttributeMetadata($attributeName);
        return $cache[$attributeName];
    }

    private function isRepeatableAttribute(string $attributeName): bool
    {
        return $this->getAttributeMetadata($attributeName)->isRepeatable;
    }

    private function getAttributeInstances(string $name, ReflectionAttribute ...$attributes): array
    {

        $result = [];

        foreach ($attributes as $reflectionAttribute) {

            try {
                $attributeName = $reflectionAttribute->getName();

                $instance = $reflectionAttribute->newInstance();

                $metaInstance = $this->getAttributeMetadata($attributeName)
                        ->withAttribute($instance)
                        ->withName($name)
                        ->withAttributeType($reflectionAttribute->getTarget());

                if ($this->isRepeatableAttribute($attributeName)) {
                    $result[$attributeName] = $result[$attributeName] ?? new RepeatableAttribute();
                    $result[$attributeName][] = $metaInstance;
                } else $result[$attributeName] = $metaInstance;
            } catch (ReflectionException $error) {
                $this->logger && $this->logger->warning('Cannot get attribute instance.', [$error]);
            }
        }


        return $result;
    }

    private function getFileInfo(string|object $class): SplFileInfo
    {
        static $cache;
        $cache = $cache ?? new WeakMap();
        //$className = is_object($class) ? get_class($class) : $class;
        return $cache[$class] = $cache[$class] ?? new SplFileInfo((new \ReflectionClass($class))->getFileName());
    }

    private function filterResults(array $input, string ...$attributeNames)
    {
        if (empty($attributeNames)) return $input;

        $result = [];
        foreach ($input as $className => $attribute) {

            foreach ($attributeNames as $attributeName) {

                if ($attributeName === $className || is_subclass_of($className, $attributeName)) {
                    $result[$className] = $attribute;
                }
            }
        }

        return $result;
    }

}
