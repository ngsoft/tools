<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use InvalidArgumentException,
    ReflectionException,
    ReflectionMethod,
    ReflectionNamedType,
    TypeError;
use function get_debug_type;

/**
 * Union Type Check support for PHP 7.4
 */
trait UnionType
{

    /**
     * Check value type against the accepted type(s)
     *
     * @param mixed $value the value to check
     * @param string $types the types to check agains, can be builtin, interfaces/class names
     * @return void
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    protected function checkType($value, string ...$types): void
    {
        static::hintType($value, ...$types);
    }

    /**
     * Check value against the accepted type(s)
     *
     * @staticvar array $checks
     * @staticvar array $replace
     * @param mixed $value the value to check
     * @param string $types the types to check agains, can be builtin, interfaces/class names
     * @return void
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    protected static function hintType($value, string ...$types): void
    {
        // check against old names and not implemented in get_debug_type
        static $checks, $replace;
        $checks = $checks ?? [
            // not in get_debug_type
            'object' => 'is_object',
            'iterable' => 'is_iterable',
            'callable' => 'is_callable',
        ];
        $replace = $replace ?? [
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'NULL' => 'null',
        ];

        if (count($types) == 0) throw new InvalidArgumentException('Invalid Argument count, at least one type is requested.');
        //replace long names (why use them?)
        $types = array_map(fn($t) => $replace[$t] ?? $t, $types);

        // support for multiple types at once, not using is_scalar check to get builtin type exception
        if (in_array('scalar', $types)) {
            $types = array_merge($types, ['int', 'float', 'bool', 'string']);
            while (($i = array_search('scalar', $types)) !== false) {
                unset($types[$i]);
            }
        }
        // not checking same types twice and if 'scalar', 'int' ...
        $types = array_unique($types);
        foreach ($types as $type) {
            if (interface_exists($type) or class_exists($type)) {
                if (is_a($value, $type)) return;
            } elseif (get_debug_type($value) == $type) return;
            elseif (isset($checks[$type]) and call_user_func_array($checks[$type], [$value])) return;
            elseif (preg_match('/^resource/', $type) > 0 and preg_match('/^resource/', get_debug_type($value)) > 0) return;
        }
        throw new TypeError(sprintf('Argument must be of the type %s, %s given', implode('|', $types), get_debug_type($value)));
    }

    /**
     * Uses ReflectionMethod to resolve method hints
     *
     * @suppress PhanUndeclaredMethod
     * @staticvar array $parsed
     * @param string $method
     * @return array
     */
    protected function parseTypes(string $method): array
    {
        // small cache (prevents using Reflection each time a property is set or unset)
        // $parsed[$className][$method]
        static $parsed;
        if (!$parsed) $parsed = [];
        $parsed[get_class($this)] = $parsed[get_class($this)] ?? [];
        $cached = &$parsed[get_class($this)];

        try {

            if (!array_key_exists($method, $cached)) {
                $cached[$method] = [];
                $params = (new ReflectionMethod($this, $method))->getParameters();
                /** @var \ReflectionParameter $param */
                foreach ($params as $id => $param) {
                    //not using parameter name as we need them ordered
                    $cached[$method][$id] = [];
                    $data = &$cached[$method][$id];
                    // params with no hint will return null
                    if ($types = $param->getType()) {
                        /** @var \ReflectionNamedType|\ReflectionUnionType $types */
                        if (method_exists($types, 'getTypes')) $types = $types->getTypes(); // Union PHP 8 support
                        else $types = [$types]; //polyfill
                        /** @var ReflectionNamedType $type */
                        foreach ($types as $type) {
                            if ($type instanceof ReflectionNamedType) $data[] = $type->getName();
                        }
                        if ($param->allowsNull()) $data[] = 'null';
                    }
                }
            }
            return $cached[$method];
        } catch (ReflectionException) {

            // we cache an empty result to prevent another error
            return $cached[$method] = [];
        }
    }

}
