<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use InvalidArgumentException,
    TypeError;
use function get_debug_type,
             str_starts_with;

/**
 * Checks for mixed union/intersection types
 */
class TypeCheck
{

    public const INTERSECTION = '&';
    public const UNION = '|';
    public const TYPE_ARRAY = 'array';
    public const TYPE_CALLABLE = 'callable';
    public const TYPE_BOOL = 'bool';
    public const TYPE_FLOAT = 'float';
    public const TYPE_INT = 'int';
    public const TYPE_STRING = 'string';
    public const TYPE_ITERABLE = 'iterable';
    public const TYPE_OBJECT = 'object';
    public const TYPE_MIXED = 'mixed';
    public const TYPE_NULL = 'null';
    public const TYPE_FALSE = 'false';
    public const TYPE_RESOURCE = 'resource';
    public const TYPE_ARRAYACCESS = 'ArrayAccess&Countable&Traversable';
    public const TYPE_STRINGABLE = 'string|Stringable';
    public const TYPE_SCALAR = 'int|bool|float|string';

    /**
     * Check the given value against the supplied types and throw TypeError if not valid
     *
     * @param string $name name to be displayed on error
     * @param mixed $value the value to check
     * @param string ...$types the types
     * @return void
     * @throws TypeError
     */
    public static function assertType(string $name, mixed $value, string ...$types): void
    {

        if ( ! self::checkType($value, ...$types)) {
            throw new TypeError(sprintf('%s must be of type %s, %s given', $name, self::getTypeString($types), get_debug_type($value)));
        }
    }

    /**
     * Can check a mix of intersection and union
     *
     *
     * eg TypeCheck::checkType([], 'Traversable & ArrayAccess | array')
     * or TypeCheck::checkType([], 'Traversable&ArrayAccess|array')
     * or TypeCheck::checkType([], \Traversable::class, '&', \ArrayAccess::class, 'array')
     * or TypeCheck::checkType([], \Traversable::class, '&', \ArrayAccess::class, '|','array')
     * or TypeCheck::checkType([], \Traversable::class, TypeCheck::INTERSECTION, \ArrayAccess::class, TypeCheck::UNION,'array')
     * the use of TypeCheck::UNION is not required
     * eg: TypeCheck::checkType([], 'string', 'object', 'array') will check 'string|object|array'
     */
    public static function checkType(mixed $value, string ...$types): bool
    {

        if (empty($types)) {
            throw new InvalidArgumentException(sprintf('%s() you must at least provide one type.', __METHOD__));
        }

        $str = self::getTypeString($types);

        foreach (explode(self::UNION, $str) as $type) {


            if (self::checkIntersectionType($value, $type)) {
                return true;
            }
        }

        return false;
    }

    protected static function getTypeString(array $types): string
    {

        $str = '';

        $keys = array_keys($types);

        foreach ($keys as $offset) {
            $previous = $types[$offset - 1] ?? null;
            $next = $types[$offset + 1] ?? null;
            // type can contains | or & or both
            $current = preg_replace('#[\h\v]+#', '', $types[$offset]);

            if (in_array($current, [self::INTERSECTION, self::UNION])) {

                if ( ! $previous || ! $next) {
                    throw new InvalidArgumentException(sprintf('%s() parameter #%s "%s" is not between 2 types.', __METHOD__, $offset, $current));
                }

                if (in_array($previous, [self::INTERSECTION, self::UNION]) || in_array($next, [self::INTERSECTION, self::UNION])) {
                    throw new InvalidArgumentException(sprintf('%s() parameter #%s: 2 types separators cannot follow themselves.', __METHOD__, $offset));
                }

                $str .= $current;
                continue;
            }

            // cleaning up for assertion purpose
            $current = trim($current, self::INTERSECTION . self::UNION);

            if (in_array($previous, [self::INTERSECTION, self::UNION]) || empty($str)) {
                $str .= $current;
                continue;
            }

            $str .= self::UNION . $current;
        }

        return $str;
    }

    protected static function checkIntersectionType(mixed $value, string $types)
    {
        if (empty($types)) {
            return false;
        }
        foreach (explode(self::INTERSECTION, $types) as $type) {

            if ( ! self::checkOneType($value, $type)) {
                return false;
            }
        }

        return true;
    }

    protected static function checkOneType(mixed $value, string $type): bool
    {
        static $builtin, $checks, $aliases;

        $builtin ??= [
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'null', 'false'
        ];
        $aliases ??= [
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'NULL' => 'null',
        ];

        $checks ??= [
            'object' => 'is_object',
            'iterable' => 'is_iterable',
            'callable' => 'is_callable',
        ];

        if ($type === 'mixed') {
            return true;
        }

        if ($type === 'scalar') {
            return in_array(get_debug_type($value), ['int', 'float', 'bool', 'string']);
        }

        $type = str_replace(array_keys($aliases), array_values($aliases), $type);

        if (isset($checks[$type])) {
            return call_user_func($checks[$type], $value);
        }

        if ($type === 'resource') {
            return str_starts_with(get_debug_type($value), 'resource');
        }

        if ($type === 'false') {
            return $value === false;
        }


        if (class_exists($type) || interface_exists($type)) {
            return is_a($value, $type);
        }


        return $type === get_debug_type($value);
    }

}
