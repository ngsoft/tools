<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use ReflectionException,
    ReflectionIntersectionType,
    ReflectionNamedType,
    ReflectionParameter,
    ReflectionProperty,
    ReflectionUnionType;
use function mb_substr;
use function NGSOFT\Tools\{
    map, some
};
use function str_contains;

/**
 * @phan-file-suppress PhanUndeclaredMethod, PhanUndeclaredStaticMethod, PhanUndeclaredProperty
 */
trait TypeParser
{

    public function isIntersectionType(): bool
    {
        return $this->getType() instanceof ReflectionIntersectionType;
    }

    public function isUnionType(): bool
    {
        return $this->getType() instanceof ReflectionUnionType;
    }

    public function isNamedType(): bool
    {
        return $this->getType() instanceof ReflectionNamedType;
    }

    public function isMixedType(): bool
    {
        return some(fn($type) => $type->getName() === 'mixed', $this->getTypes());
    }

    /**
     * @return Type[]
     */
    public function getTypes(): array
    {

        $types = $this->getType();

        if (null === $types) {
            $types = 'mixed';
        }

        $str = (string) $types;

        $result = [];

        if ($str[0] === '?') {
            $str = mb_substr($str, 1);
            $result['null'] = 'null';
        }

        foreach (preg_split('#[\&\|]+#', $str) as $type) {
            $result[$type] = $type;
        }

        return map(fn($type) => Type::create($type), array_values($result));
    }

    public function hasDefault(): bool
    {
        return $this->reflector instanceof ReflectionProperty ? $this->hasDefaultValue() : $this->isDefaultValueAvailable(); ;
    }

    public function getDefault(): mixed
    {
        return $this->parseDefaultValue($this->reflector);
    }

    protected function parseDefaultValue(ReflectionParameter|ReflectionProperty $reflector): mixed
    {

        $hasDefault = $reflector instanceof ReflectionProperty ? $reflector->hasDefaultValue() : $reflector->isDefaultValueAvailable();
        $nullable = $reflector->getType() === null || $reflector->getType()->allowsNull() || str_contains((string) $reflector->getType(), '?');

        if ($reflector instanceof ReflectionParameter && $reflector->isVariadic()) {
            return [];
        }

        if ($hasDefault) {
            return $reflector->getDefaultValue();
        }

        if ($nullable) {
            return null;
        }

        throw new ReflectionException(
                        sprintf('Cannot get default value for %s $%s type %s',
                                $reflector instanceof ReflectionProperty ? 'property' : 'parameter',
                                $reflector->getName(), (string) $reflector->getType())
        );
    }

}
