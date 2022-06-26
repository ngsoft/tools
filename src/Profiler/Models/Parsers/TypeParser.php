<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models\Parsers;

use NGSOFT\Profiler\Models\{
    BaseModel, Type
};
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

abstract class TypeParser extends BaseModel
{

    protected ?string $typeClass = null;

    protected function getTypeClass(): string
    {

        if (is_null($this->typeClass)) {
            $type = $this->getType();
            $this->typeClass = is_null($type) ? 'null' : get_class($type);
        }
        return $this->typeClass;
    }

    public function isIntersectionType(): bool
    {
        return $this->getTypeClass() === ReflectionIntersectionType::class;
    }

    public function isUnionType(): bool
    {
        return $this->getTypeClass() === ReflectionUnionType::class;
    }

    public function isNamedType(): bool
    {
        return $this->getTypeClass() === ReflectionNamedType::class;
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

        return map(fn($type) => Type::create($type), $result);
    }

    public function hasDefault(): bool
    {
        return $this->reflector instanceof ReflectionProperty ? $this->hasDefaultValue() : $this->isDefaultValueAvailable();
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
