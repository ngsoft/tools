<?php

declare(strict_types=1);

use NGSOFT\Tools;

if ( ! defined('NAMESPACE_SEPARATOR')) {
    define('NAMESPACE_SEPARATOR', '\\');
}

if ( ! defined('PHP_EXT')) {
    define('PHP_EXT', '.php');
}

if ( ! defined('SCRIPT_START')) {
    define('SCRIPT_START', $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));
}

if ( ! defined('DATE_DB')) {
    define('DATE_DB', 'Y-m-d H:i:s');
}

if ( ! function_exists('class_namespace')) {

    /**
     * Get the namespace from a class
     *
     * @param string|object $class
     * @return string
     */
    function class_namespace(string|object $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        if ( ! str_contains($class, NAMESPACE_SEPARATOR)) {
            return '';
        }
        return substr($class, 0, strrpos($class, NAMESPACE_SEPARATOR));
    }

}


if ( ! function_exists('is_stringable')) {

    function is_stringable(mixed $value): bool
    {
        if (is_scalar($value) || is_null($value)) {
            return true;
        }
        if ($value instanceof Stringable) {
            return true;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return true;
        }

        return false;
    }

}

if ( ! function_exists('uses_trait')) {

    /**
     * Checks recursively if a class uses a trait
     *
     * @param string|object $class
     * @param string $trait
     * @return bool
     */
    function uses_trait(string|object $class, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($class));
    }

}


if ( ! function_exists('implements_class')) {

    /**
     * Get class implementing given parent class from the loaded classes
     *
     * @param string|object $parentClass
     * @param bool $instanciable
     * @return array
     * @throws InvalidArgumentException
     */
    function implements_class(string|object $parentClass, bool $instanciable = true): array
    {
        return Tools::implements_class($parentClass, $instanciable);
    }

}


if ( ! function_exists('get_class_constants')) {


    /**
     * Get Constants defined in a class recursively
     *
     * @param string|object $class
     * @param bool $public if True returns only public visibility constants
     * @return array
     */
    function get_class_constants(string|object $class, bool $public = true): array
    {
        return Tools::getClassConstants($class, $public);
    }

}


if ( ! function_exists('is_instanciable')) {

    function is_instanciable(string $class): bool
    {
        return class_exists($class) && (new \ReflectionClass($class))->isInstantiable();
    }

}

if ( ! function_exists('random_string')) {

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     */
    function random_string(int $length = 16): string
    {
        return Tools::randomString($length);
    }

}


if ( ! function_exists('preg_valid')) {

    /**
     * Check if regular expression is valid
     *
     * @phan-suppress PhanParamSuspiciousOrder
     */
    function preg_valid(string $pattern, bool $exception = false): bool
    {
        try {
            set_default_error_handler();
            return $pattern !== ltrim($pattern, '%#/') && preg_match($pattern, '') !== false; // must be >=0 to be correct
        } catch (ErrorException $error) {
            if ($exception) {
                $msg = str_replace('_match', '_valid', $error->getMessage());
                throw new WarningException($msg, previous: $error);
            }
            return false;
        } finally { restore_error_handler(); }
    }

}

if ( ! function_exists('preg_test')) {

    /**
     * Test if subject matches the pattern
     */
    function preg_test(string $pattern, string $subject): bool
    {
        preg_valid($pattern, true);
        return preg_match($pattern, $subject) > 0;
    }

}

if ( ! function_exists('preg_exec')) {

    /**
     * Perform a regular expression match
     *
     * @param string $pattern the regular expression
     * @param string $subject the subject
     * @param int $limit maximum number of results if set to 0, all results are returned
     * @return array
     */
    function preg_exec(string $pattern, string $subject, int $limit = 1): array
    {
        preg_valid($pattern, true);

        $limit = max(0, $limit);

        if (preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER) > 0) {

            if ($limit === 0) {
                $limit = count($matches);
            }

            if ($limit === 1) {
                return $matches[0];
            }

            while (count($matches) > $limit) {
                array_pop($matches);
            }
            return $matches;
        }

        return [];
    }

}

if ( ! function_exists('in_range')) {

    /**
     * Checks if number is in range
     */
    function in_range(int $number, int $min, int $max, bool $inclusive = true)
    {

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }


        return
                $inclusive ?
                ($number >= $min && $number <= $max) :
                ($number > $min && $number < $max);
    }

}


if ( ! function_exists('wait')) {

    /**
     * Wait for a given amount of time
     *
     * @param int $ms if 0 wait for 90 to 110 ms
     * @return void
     */
    function wait(int $ms = 0): void
    {
        if ($ms === 0) {
            $ms = 100 + random_int(-10, 10);
        }

        usleep($ms * 1000);
    }

}

if ( ! function_exists('until')) {


    /**
     * Execute callback until condition is met
     *
     * @param callable $condition must returns non blank value for success
     * @param int $times maximum times the loop can run
     * @param int $waitMs time to wait between attempts
     * @return bool Success or failure
     */
    function until(callable $condition, int $times = 1000, int $waitMs = 10): bool
    {

        while ($times > 0) {
            if (filled($condition())) {
                return true;
            }
            wait($waitMs);
            $times --;
        }
        return false;
    }

}


if ( ! function_exists('call_private_method')) {

    /**
     * Call a non static method inside an object ignoring its restrictions
     */
    function call_private_method(object $instance, string $method, mixed ...$arguments): mixed
    {
        return Tools::callPrivateMethod($instance, $method, ...$arguments);
    }

}


if ( ! function_exists('require_package')) {


    /**
     * Helper to define an optionnal composer required package
     *
     * @link https://getcomposer.org/doc/04-schema.md#json-schema
     *
     * @param string $package Composer package name with version eg: ngsoft/tools:^3
     * @param string $classCheck A class/interface/trait present in the package to check if exists
     * @param string $exceptionClass the exception class to throw if not present
     * @return void
     * @throws InvalidArgumentException if package name is incorrect
     */
    function require_package(string $package, string $classCheck, string $exceptionClass = RuntimeException::class): void
    {

        list($name) = explode(':', $package);

        if (preg_match('#^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$#', $name) === false) {
            throw new InvalidArgumentException("Invalid package name: {$name}");
        }

        if ( ! class_exists($classCheck) && ! interface_exists($classCheck) && ! trait_exists($classCheck)) {
            throw new $exceptionClass(
                            sprintf('Composer package %s not installed, please run: composer require %s', $name, $package)
            );
        }
    }

}