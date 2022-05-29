<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use Attribute,
    InvalidArgumentException,
    ReflectionAttribute,
    ReflectionClass,
    ReflectionIntersectionType,
    ReflectionNamedType,
    ReflectionParameter,
    ReflectionUnionType,
    RuntimeException,
    Throwable,
    ValueError;

class AttributeMetadata
{

    public readonly string $attributeName;
    public readonly bool $targetClass;
    public readonly bool $targetClassConstant;
    public readonly bool $targetProperty;
    public readonly bool $targetMethod;
    public readonly bool $targetFunction;
    public readonly bool $targetParameter;
    public readonly bool $isRepeatable;
    public readonly int $flags;
    public readonly array $parameters;

    public function __construct(
            string $attributeName,
            public ?object $attribute = null,
            public ?AttributeType $attributeType = null
    )
    {

        static $cache = [];

        if (!isset($cache[$attributeName])) {

            try {
                $reflectionClass = new ReflectionClass($attributeName);
                foreach ($reflectionClass->getAttributes(Attribute::class, ReflectionAttribute::IS_INSTANCEOF) as $reflectionAttribute) {
                    $attribute = $reflectionAttribute->newInstance();
                    $targetClass = ($attribute->flags & Attribute::TARGET_CLASS) > 0;
                    $targetClassConstant = ($attribute->flags & Attribute::TARGET_CLASS_CONSTANT) > 0;
                    $targetProperty = ($attribute->flags & Attribute::TARGET_PROPERTY) > 0;
                    $targetMethod = ($attribute->flags & Attribute::TARGET_METHOD) > 0;
                    $targetFunction = ($attribute->flags & Attribute::TARGET_FUNCTION) > 0;
                    $targetParameter = ($attribute->flags & Attribute::TARGET_PARAMETER) > 0;
                    $isRepeatable = ($attribute->flags & Attribute::IS_REPEATABLE) > 0;

                    $parameters = $this->parseParameters($reflectionClass);

                    $cache[$attributeName] = [
                        $attributeName,
                        $targetClass,
                        $targetClassConstant,
                        $targetProperty,
                        $targetMethod,
                        $targetFunction,
                        $targetParameter,
                        $isRepeatable,
                        $attribute->flags,
                        $parameters,
                    ];

                    break;
                }

                if (!isset($attribute)) throw new ValueError(sprintf('%s is not a valid attribute name.', $attributeName));
            } catch (Throwable $previous) {
                throw new RuntimeException(sprintf('Cannot read attribute %s metadata.', $attributeName), 0, $previous);
            }
        }

        list(
                $this->attributeName,
                $this->targetClass,
                $this->targetClassConstant,
                $this->targetProperty,
                $this->targetMethod,
                $this->targetFunction,
                $this->targetParameter,
                $this->isRepeatable,
                $this->flags,
                $this->parameters
                ) = $cache[$attributeName];
    }

    public function withAttribute(object $attribute): static
    {
        $clone = clone $this;
        if (get_class($attribute) !== $this->attributeName) {
            throw new InvalidArgumentException(sprintf('Invalid Attribute "%s" set for %s', $attribute::class, $this->attributeName));
        }
        $clone->attribute = $attribute;
        return $clone;
    }

    public function withAttributeType(AttributeType|int $attributeType): static
    {
        $clone = clone $this;
        $clone->attributeType = is_int($attributeType) ? AttributeType::from($attributeType) : $attributeType;
        return $clone;
    }

    /**
     * @phan-suppress PhanUndeclaredMethod
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    private function parseParameters(\ReflectionClass $reflectionClass): array
    {
        $result = [];
        if ($reflectionClass->hasMethod('__construct')) {
            $reflectionMethod = $reflectionClass->getMethod('__construct');

            /** @var ReflectionParameter $reflectionParameter */
            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {

                $type = 'mixed';

                if ($reflectionType = $reflectionParameter->getType()) {
                    /** @var ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType $reflectionType */
                    if ($reflectionType instanceof ReflectionNamedType) {
                        $type = $reflectionType->getName();
                    } else {
                        $types = array_map(fn($t) => $t->getName(), $reflectionType->getTypes());
                        $type = implode($reflectionType instanceof ReflectionUnionType ? '|' : '&', $types);
                    }
                } else $type = 'mixed';
                $result[$reflectionParameter->getName()] = new AttributeParameter($reflectionParameter->getName(), $type);
            }
        }
        return $result;
    }

    public function __serialize(): array
    {

        return [
            $this->attributeName,
            $this->targetClass,
            $this->targetClassConstant,
            $this->targetProperty,
            $this->targetMethod,
            $this->targetFunction,
            $this->targetParameter,
            $this->isRepeatable,
            $this->flags,
            $this->parameters,
            $this->attribute,
            $this->attributeType,
        ];
    }

    public function __unserialize(array $data): void
    {
        list(
                $this->attributeName,
                $this->targetClass,
                $this->targetClassConstant,
                $this->targetProperty,
                $this->targetMethod,
                $this->targetFunction,
                $this->targetParameter,
                $this->isRepeatable,
                $this->flags,
                $this->parameters,
                $this->attribute,
                $this->attributeType,
                ) = $data;
    }

}
