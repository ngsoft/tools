<?php

declare(strict_types=1);

namespace {

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
}

namespace NGSOFT\Tools {

    use NGSOFT\Tools;

    const MICROSECOND = 1e-6;
    const MILLISECOND = 1e-3;
    const SECOND = 1;
    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
    const MONTH = 2628000;
    const YEAR = 31536000;

    /**
     * Tests if at least one element in the iterable passes the test implemented by the provided function.
     * @param callable $callback
     * @param iterable $iterable
     * @return bool
     */
    function some(callable $callback, iterable $iterable): bool
    {
        return Tools::some($callback, $iterable);
    }

    /**
     * Tests if all elements in the iterable pass the test implemented by the provided function.
     * @param callable $callback
     * @param iterable $iterable
     * @return bool
     */
    function every(callable $callback, iterable $iterable): bool
    {
        return Tools::every($callback, $iterable);
    }

    /**
     * Same as the original except callback accepts more arguments and works with string keys
     * @param callable $callback accepts $value, $key, $array
     * @param iterable $iterable
     * @return array
     */
    function map(callable $callback, iterable $iterable): array
    {
        return Tools::map($callback, $iterable);
    }

    /**
     * Filters elements of an iterable using a callback function
     *
     * @param callable $callback accepts $value, $key, $array
     * @param iterable $iterable
     * @return array
     */
    function filter(callable $callback, iterable $iterable): array
    {
        return Tools::filter($callback, $iterable);
    }

    /**
     * Uses callback for each elements of the array and returns the value
     *
     * @param callable $callback
     * @param iterable $iterable
     * @return iterable
     */
    function each(callable $callback, iterable $iterable): iterable
    {
        yield from Tools::each($callback, $iterable);
    }

    /**
     * Change the current active directory
     * And stores the last position, use popd() to return to previous directory
     * @param string $dir
     * @return bool
     */
    function pushd(string $dir): bool
    {
        return Tools::pushd($dir);
    }

    /**
     * Restore the last active directory changed by pushd
     * @return string|false current directory
     */
    function popd(): string|false
    {
        return Tools::popd();
    }

    /**
     * Pauses script execution for a given amount of time
     * uses sleep or usleep
     *
     * @param int|float $seconds
     */
    function pause(int|float $seconds): void
    {
        Tools::pause($seconds);
    }

    /**
     * Pauses script execution for a given amount of milliseconds
     *
     * @param int $milliseconds
     * @return void
     */
    function msleep(int $milliseconds): void
    {
        Tools::msleep($milliseconds);
    }

}

