<?php

declare(strict_types=1);

use NGSOFT\Tools;

if ( ! defined('NAMESPACE_SEPARATOR')) {
    define('NAMESPACE_SEPARATOR', '\\');
}

if ( ! defined('SCRIPT_START')) {
    define('SCRIPT_START', $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));
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
        if (is_scalar($value) || null === $value) {
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
        try {
            return (new \ReflectionClass($class))->isInstantiable();
        } catch (\Throwable) {

        }

        return false;
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

if ( ! function_exists('wait_for')) {

    /**
     * Wait for a given amount of time
     *
     * @param int $ms if 0 wait for .9 to 110 ms
     * @return void
     */
    function wait_for(int $ms = 0): void
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
     * @param callable $contition must returns non blank value for success
     * @param int $times maximum times the loop can run
     * @param int $waitForMs time to wait between attempts
     * @return bool Success or failure
     */
    function until(callable $contition, int $times = 1000, int $waitForMs = 10): bool
    {

        while ($times > 0) {
            if (filled($contition())) {
                return true;
            }
            wait_for($waitForMs);
            $times --;
        }
        return false;
    }

}


if ( ! function_exists('call_private_method')) {

    function call_private_method(object $instance, string $method, mixed ...$arguments): mixed
    {
        return Tools::callPrivateMethod($instance, $method, ...$arguments);
    }

}