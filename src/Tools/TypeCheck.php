<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

/**
 * Checks for mixed union/intersection types
 */
class TypeCheck
{

    public static function checkType(mixed $value, string ...$types): bool
    {

    }

    public static function checkUnionType(mixed $value, string|UnionType ...$types): bool
    {
        if (empty($types)) {
            return false;
        }
    }

    public static function checkIntersectionType(mixed $value, string|IntersectionType ...$types)
    {

    }

    protected static function parseType(string $type): array
    {

    }

}
