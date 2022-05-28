<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use Psr\{
    Cache\CacheItemPoolInterface, Log\LoggerAwareTrait
};
use ReflectionAttribute,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionException,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty,
    Throwable,
    ValueError;

class AttributeReader {

    use LoggerAwareTrait;

    private ?CacheItemPoolInterface $cachePool = null;

    public function getClassAttributes(string|object $className, int|AttributeType $type = AttributeType::ATTRIBUTE_ALL, string ...$attributeNames): array {

        /** @var AttributeType $target */
        $target = $type instanceof AttributeType ? $type : AttributeType::from($type);

        $targetInt = $target->value;

        $result = [];
        try {

            if ($target->is(AttributeType::ATTRIBUTE_FUNCTION) || $target->is(AttributeType::ATTRIBUTE_PARAMETER)) {
                throw new ValueError(sprintf('Invalid type %s::%s defined for %s', AttributeType::class, $target->name, __METHOD__));
            }

            $reflectionClass = new ReflectionClass($className);

            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_CLASS) {
                $result[AttributeType::ATTRIBUTE_CLASS()->name] = $this->getReflectionClassAttributes($reflectionClass);
            }
            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_CLASS_CONSTANT) {
                $result[AttributeType::ATTRIBUTE_CLASS_CONSTANT()->name] = [];
                /** @var ReflectionClassConstant $reflectionClassConstant */
                foreach ($reflectionClass->getReflectionConstants() as $reflectionClassConstant) {
                    $name = $reflectionClassConstant->getName();
                    $result[AttributeType::ATTRIBUTE_CLASS_CONSTANT()->name][$name] = $this->getReflectionClassConstantAttributes($reflectionClassConstant);
                }
            }
            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_PROPERTY) {
                $result[AttributeType::ATTRIBUTE_PROPERTY()->name] = [];
                /** @var ReflectionProperty $reflectionProperty */
                foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                    $name = $reflectionProperty->getName();
                    $result[AttributeType::ATTRIBUTE_PROPERTY()->name][$name] = $this->getReflectionPropertyAttributes($reflectionProperty);
                }
            }
            if ($targetInt === AttributeType::ATTRIBUTE_ALL || $targetInt === AttributeType::ATTRIBUTE_METHOD) {
                $result[AttributeType::ATTRIBUTE_METHOD()->name] = [];
                /** @var ReflectionMethod $reflectionMethod */
                foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                    $name = $reflectionMethod->getName();
                    $result[AttributeType::ATTRIBUTE_METHOD()->name][$name] = $this->getReflectionMethodAttributes($reflectionMethod);
                    if ($targetInt === AttributeType::ATTRIBUTE_ALL) {
                        $result[AttributeType::ATTRIBUTE_METHOD()->name][$name][AttributeType::ATTRIBUTE_PARAMETER()->name] = [];
                        /** @var ReflectionParameter $reflectionParameter */
                        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                            $name = $reflectionParameter->getName();
                            $result[AttributeType::ATTRIBUTE_METHOD()->name][$name][AttributeType::ATTRIBUTE_PARAMETER()->name][$name] = $this->getReflectionParameterAttributes($reflectionParameter);
                        }
                    }
                }
            }
        } catch (Throwable $error) {
            $this->logger && $this->logger->warning(sprintf('Cannot get attributes for class %s', is_object($className) ? $className::class : $className), [$error]);
        }

        return $result;
    }

    public function getReflectionClassAttributes(ReflectionClass $reflectionClass): array {
        return $this->getAttributeInstances(...$reflectionClass->getAttributes());
    }

    public function getReflectionMethodAttributes(ReflectionMethod $reflectionMethod): array {
        return $this->getAttributeInstances(...$reflectionMethod->getAttributes());
    }

    public function getReflectionPropertyAttributes(ReflectionProperty $reflectionProperty): array {
        return $this->getAttributeInstances(...$reflectionProperty->getAttributes());
    }

    public function getReflectionClassConstantAttributes(ReflectionClassConstant $reflectionClassConstant): array {
        return $this->getAttributeInstances(...$reflectionClassConstant->getAttributes());
    }

    public function getReflectionFunctionAttributes(ReflectionFunction $reflectionFunction): array {
        return $this->getAttributeInstances(...$reflectionFunction->getAttributes());
    }

    public function getReflectionParameterAttributes(ReflectionParameter $reflectionParameter) {
        return $this->getAttributeInstances(...$reflectionParameter->getAttributes());
    }

    private function getAttributeMetadata(string $attributeName): AttributeMetadata {

        static $cache = [];
        $cache[$attributeName] = $cache[$attributeName] ?? new AttributeMetadata($attributeName);
        return $cache[$attributeName];
    }

    private function isRepeatableAttribute(string $attributeName): bool {
        return $this->getAttributeMetadata($attributeName)->isRepeatable;
    }

    private function getAttributeInstances(ReflectionAttribute ...$attributes): array {

        $result = [];

        foreach ($attributes as $reflectionAttribute) {

            try {
                $attributeName = $reflectionAttribute->getName();
                $instance = $reflectionAttribute->newInstance();

                if ($this->isRepeatableAttribute($attributeName)) {
                    $result[$attributeName] = $result[$attributeName] ?? new RepeatableAttribute();
                    $result[$attributeName][] = $instance;
                } else $result[$attributeName] = $instance;
            } catch (ReflectionException $error) {
                $this->logger && $this->logger->warning('Cannot get attribute instance.', [$error]);
            }
        }


        return $result;
    }

}
