<?php

declare(strict_types=1);

use NGSOFT\Tools;

if ( ! defined('NAMESPACE_SEPARATOR'))
{
    define('NAMESPACE_SEPARATOR', '\\');
}

if ( ! defined('PHP_EXT'))
{
    define('PHP_EXT', '.php');
}

if ( ! defined('SCRIPT_START'))
{
    define('SCRIPT_START', $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));
}

if ( ! defined('DATE_DB'))
{
    define('DATE_DB', 'Y-m-d H:i:s');
}

if ( ! function_exists('class_namespace'))
{
    /**
     * Get the namespace from a class.
     */
    function class_namespace(object|string $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        if ( ! str_contains($class, NAMESPACE_SEPARATOR))
        {
            return '';
        }
        return substr($class, 0, strrpos($class, NAMESPACE_SEPARATOR));
    }
}

if ( ! function_exists('is_stringable'))
{
    /**
     * Checks if value can be converted to string.
     */
    function is_stringable(mixed $value): bool
    {
        if (is_scalar($value) || is_null($value))
        {
            return true;
        }

        if ($value instanceof Stringable)
        {
            return true;
        }

        if (is_object($value) && method_exists($value, '__toString'))
        {
            return true;
        }

        return false;
    }
}

if ( ! function_exists('str_val'))
{
    /**
     * Get string value of a variable.
     */
    function str_val(mixed $value): string
    {
        if (is_string($value))
        {
            return $value;
        }

        if (is_null($value))
        {
            return '';
        }

        if (is_bool($value))
        {
            return $value ? 'true' : 'false';
        }

        if (is_numeric($value))
        {
            return (string) $value;
        }

        if ( ! is_stringable($value))
        {
            throw new InvalidArgumentException(sprintf('Text of type %s is not stringable.', get_debug_type($value)));
        }

        return (string) $value;
    }
}

if ( ! function_exists('is_arrayaccess'))
{
    /**
     * Check if value is Array like.
     */
    function is_arrayaccess(mixed $value): bool
    {
        if (is_array($value))
        {
            return true;
        }

        return $value instanceof ArrayAccess && $value instanceof Countable;
    }
}

if ( ! function_exists('is_unsigned'))
{
    /**
     * Checks if value is not negative.
     */
    function is_unsigned(float|int $value): bool
    {
        return $value >= 0;
    }
}

if ( ! function_exists('uses_trait'))
{
    /**
     * Checks recursively if a class uses a trait.
     */
    function uses_trait(object|string $class, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($class));
    }
}

if ( ! function_exists('implements_class'))
{
    /**
     * Get class implementing given parent class from the loaded classes.
     *
     * @throws InvalidArgumentException
     */
    function implements_class(object|string $parentClass, bool $instanciable = true): array
    {
        return Tools::implements_class($parentClass, $instanciable);
    }
}

if ( ! function_exists('get_class_constants'))
{
    /**
     * Get Constants defined in a class recursively.
     *
     * @param bool $public if True returns only public visibility constants
     */
    function get_class_constants(object|string $class, bool $public = true): array
    {
        return Tools::getClassConstants($class, $public);
    }
}

if ( ! function_exists('is_instantiable'))
{
    function is_instantiable(string $class): bool
    {
        return class_exists($class) && (new ReflectionClass($class))->isInstantiable();
    }
}

if ( ! function_exists('random_string'))
{
    /**
     * Generate a more truly "random" alpha-numeric string.
     */
    function random_string(int $length = 16): string
    {
        return Tools::randomString($length);
    }
}

if ( ! function_exists('preg_valid'))
{
    /**
     * Check if regular expression is valid.
     *
     * @phan-suppress PhanParamSuspiciousOrder
     */
    function preg_valid(string $pattern, bool $exception = false): bool
    {
        try
        {
            set_default_error_handler();
            return $pattern !== ltrim($pattern, '%#/') && false !== preg_match($pattern, ''); // must be >=0 to be correct
        } catch (ErrorException $error)
        {
            if ($exception)
            {
                $msg = str_replace('_match', '_valid', $error->getMessage());
                throw new WarningException($msg, previous: $error);
            }
            return false;
        } finally
        {
            restore_error_handler();
        }
    }
}

if ( ! function_exists('preg_test'))
{
    /**
     * Test if subject matches the pattern.
     */
    function preg_test(string $pattern, string $subject): bool
    {
        preg_valid($pattern, true);
        return preg_match($pattern, $subject) > 0;
    }
}

if ( ! function_exists('preg_exec'))
{
    /**
     * Perform a regular expression match.
     *
     * @param string $pattern the regular expression
     * @param string $subject the subject
     * @param int    $limit   maximum number of results if set to 0, all results are returned
     */
    function preg_exec(string $pattern, string $subject, int $limit = 1): array
    {
        preg_valid($pattern, true);

        $limit = max(0, $limit);

        if (preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER) > 0)
        {
            if (0 === $limit)
            {
                $limit = count($matches);
            }

            if (1 === $limit)
            {
                return $matches[0];
            }

            while (count($matches) > $limit)
            {
                array_pop($matches);
            }
            return $matches;
        }

        return [];
    }
}

if ( ! function_exists('in_range'))
{
    /**
     * Checks if number is in range.
     */
    function in_range(float|int $number, float|int $min, float|int $max, bool $inclusive = true): bool
    {
        if ($min === $max)
        {
            return $number === $min && $inclusive;
        }

        if ($min > $max)
        {
            [$min, $max] = [$max, $min];
        }

        if ($inclusive)
        {
            return $number >= $min && $number <= $max;
        }

        return $number > $min && $number < $max;
    }
}

if ( ! function_exists('length'))
{
    /**
     * Get the length of a scalar|array|Countable.
     */
    function length(mixed $value): int
    {
        if ( ! is_scalar($value) && ! is_countable($value))
        {
            throw new TypeError(sprintf('object of type %s has no length.', get_debug_type($value)));
        }

        return match (get_debug_type($value))
        {
            'bool'   => $value ? 1 : 0,
            'int', 'float' => $value > 0 ? (int) $value : 0,
            'string' => '' === $value ? 0 : mb_strlen($value),
            default  => count($value),
        };
    }
}

if ( ! function_exists('wait'))
{
    /**
     * Wait for a given amount of time.
     *
     * @param int $ms if 0 wait for 90 to 110 ms
     */
    function wait(int $ms = 0): void
    {
        if (0 === $ms)
        {
            $ms = 100 + random_int(-10, 10);
        }

        usleep($ms * 1000);
    }
}

if ( ! function_exists('until'))
{
    /**
     * Execute callback until condition is met.
     *
     * @param callable $condition must returns non blank value for success
     * @param int      $times     maximum times the loop can run
     * @param int      $waitMs    time to wait between attempts
     *
     * @return bool Success or failure
     */
    function until(callable $condition, int $times = 1000, int $waitMs = 10): bool
    {
        while ($times > 0)
        {
            if (filled($condition()))
            {
                return true;
            }
            wait($waitMs);
            --$times;
        }
        return false;
    }
}

if ( ! function_exists('call_private_method'))
{
    /**
     * Call a non-static method inside an object ignoring its restrictions.
     */
    function call_private_method(object $instance, string $method, mixed ...$arguments): mixed
    {
        return Tools::callPrivateMethod($instance, $method, ...$arguments);
    }
}

if ( ! function_exists('require_package'))
{
    /**
     * Helper to define an optionnal composer required package.
     *
     * @see https://getcomposer.org/doc/04-schema.md#json-schema
     *
     * @param string $package        Composer package name with version eg: ngsoft/tools:^3
     * @param string $classCheck     A class/interface/trait present in the package to check if exists
     * @param string $exceptionClass the exception class to throw if not present
     *
     * @throws InvalidArgumentException if package name is incorrect
     */
    function require_package(string $package, string $classCheck, string $exceptionClass = RuntimeException::class): void
    {
        list($name) = explode(':', $package);

        if (false === preg_match('#^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$#', $name))
        {
            throw new InvalidArgumentException("Invalid package name: {$name}");
        }

        if ( ! class_exists($classCheck) && ! interface_exists($classCheck) && ! trait_exists($classCheck))
        {
            throw new $exceptionClass(
                sprintf('Composer package %s not installed, please run: composer require %s', $name, $package)
            );
        }
    }
}

if ( ! function_exists('array_get'))
{
    /**
     * Get an item from an array like using "dot" notation.
     * Same as object_get() but works with array like.
     */
    function array_get(array|ArrayAccess $array, ?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key) || '' === trim($key))
        {
            return $array;
        }

        foreach (explode('.', $key) as $segment)
        {
            if ( ! isset($array, $array[$segment]))
            {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}
