<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use ReflectionIntersectionType,
    ReflectionNamedType,
    ReflectionUnionType;
use function mb_substr,
             NGSOFT\Tools\map;

/**
 * @phan-file-suppress PhanUndeclaredMethod, PhanUndeclaredStaticMethod
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
        return in_array('mixed', $this->getTypes());
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

}
