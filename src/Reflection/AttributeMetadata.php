<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use Attribute,
    ReflectionAttribute,
    ReflectionClass,
    RuntimeException,
    Throwable,
    ValueError;

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
    public readonly array $parameters;

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
                    $parameters = [];

                    if (method_exists($attributeName, '__construct')) {
                        $reflectionMethod = $reflectionClass->getMethod('__construct');

                        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                            $parameters[] = $reflectionParameter->getName();
                        }
                    }

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
            $this->flags,
            $this->parameters
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
                $this->flags,
                $this->parameters
                ) = $data;
    }

}
