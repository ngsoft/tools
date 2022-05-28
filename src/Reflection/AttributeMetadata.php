<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use Attribute,
    ReflectionAttribute,
    ReflectionClass,
    ReflectionException,
    RuntimeException;

class AttributeMetadata {

    public readonly string $attributeName;
    public readonly bool $targetClass;
    public readonly bool $targetClassConstant;
    public readonly bool $targetProperty;
    public readonly bool $targetMethod;
    public readonly bool $targetFunction;
    public readonly bool $targetParameter;
    public readonly bool $isRepeatable;
    public readonly int $flags;

    public function __construct(string $attributeName) {

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

                    $cache[$attributeName] = [
                        $attributeName,
                        $targetClass,
                        $targetClassConstant,
                        $targetProperty,
                        $targetMethod,
                        $targetFunction,
                        $targetParameter,
                        $isRepeatable,
                        $attribute->flags
                    ];

                    break;
                }
            } catch (ReflectionException $previous) {
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
                $this->flags
                ) = $cache[$attributeName];
    }

    public function __serialize(): array {

        return [
            $this->attributeName,
            $this->targetClass,
            $this->targetClassConstant,
            $this->targetProperty,
            $this->targetMethod,
            $this->targetFunction,
            $this->targetParameter,
            $this->isRepeatable,
            $this->flags
        ];
    }

    public function __unserialize(array $data): void {
        list(
                $this->attributeName,
                $this->targetClass,
                $this->targetClassConstant,
                $this->targetProperty,
                $this->targetMethod,
                $this->targetFunction,
                $this->targetParameter,
                $this->isRepeatable,
                $this->flags
                ) = $data;
    }

}
