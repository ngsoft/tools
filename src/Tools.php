<?php

declare(strict_types=1);

namespace NGSOFT;

use ErrorException,
    InvalidArgumentException,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionException,
    RuntimeException;
use const SCRIPT_START;
use function mb_substr;

/**
 * Useful Functions to use in my projects
 */
final class Tools
{

    /**
     * Package Version Information
     */
    public const VERSION = '3.0';

    /**
     * URL Parser Regex
     * @link https://gist.github.com/dperini/729294 (with protocol required)
     */
    public const WEB_URL_REGEX = '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[\/?#]\S*)?$/iu';
    public const LOCAL_URL_REGEX = '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:\.?[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}])+(?:(?:\.[a-z\x{00a1}-\x{ffff}]{2,})\.?)?)(?::\d{2,5})?(?:[\/?#]\S*)?$/iu';

    /**
     * Time Constants (in seconds)
     */
    public const MICROSECOND = 1e-6;
    public const MILLISECOND = 1e-3;
    public const SECOND = 1;
    public const MINUTE = 60;
    public const HOUR = 3600;
    public const DAY = 86400;
    public const WEEK = 604800;
    public const MONTH = 2628000;
    public const YEAR = 31536000;

    /**
     * Size Constants
     * in bytes
     */
    public const KB = 1024;
    public const MB = 1048576;
    public const GB = 1073741824;
    public const TB = 1099511627776;

    /**
     * Stores pushd history
     * @var string[]
     */
    private static $pushd_history = [];

    /**
     * Execute a callback and hides all php errors that can be thrown
     * Exceptions thrown inside the callback will be preserved
     *
     * @param callable $callback
     * @param mixed $args args to be passed to the callback
     * @return mixed
     */
    public static function safe_exec(callable $callback, mixed ...$args): mixed
    {
        try {
            self::suppress_errors();
            return $callback(...$args);
        } finally { restore_error_handler(); }
    }

    /**
     * Convenient Function used to convert php errors, warning, ... as Throwable
     * @return callable|null
     */
    public static function errors_as_exceptions(): callable|null
    {
        return set_error_handler(function ($type, $msg, $file, $line) {
            if ( ! (error_reporting() & $type)) { return false; }
            throw new ErrorException($msg, 0, $type, $file, $line);
        });
    }

    /**
     * Set error handler to empty closure (as of php 8.1 @ doesn't works anymore)
     *
     * @phan-suppress PhanTypeMismatchArgumentInternal
     * @return callable|null
     */
    public static function suppress_errors(): callable|null
    {
        static $handler;
        $handler = $handler ?? static fn() => null;
        return set_error_handler($handler);
    }

    /**
     * Normalize pathnames
     *
     * @param string $path
     * @return string
     */
    public static function normalize_path(string $path): string
    {
        if (empty($path)) {
            return $path;
        }

        $path = preg_replace('#[\\\/]+#', DIRECTORY_SEPARATOR, $path);

        if (in_array($path[-1], ['\\', '/'])) {
            $path = mb_substr($path, 0, -1);
        }


        return $path;
    }

    /**
     * Change the current active directory
     * And stores the last position, use popd() to return to previous directory
     * @param string $dir
     * @return bool
     */
    public static function pushd(string $dir): bool
    {
        try {
            self::suppress_errors();
            if (($current = getcwd()) && chdir($dir)) {
                self::$pushd_history[] = $current;
                return true;
            }
        } finally { restore_error_handler(); }
        return false;
    }

    /**
     * Restore the last active directory changed by pushd
     * @return string|false current directory
     */
    public static function popd(): string|false
    {

        try {
            self::suppress_errors();
            $previous = array_pop(self::$pushd_history) ?? getcwd();
            return chdir($previous) ? $previous : false;
        } finally { restore_error_handler(); }
    }

    /**
     * Get class implementing given parent class from the loaded classes
     *
     * @param string|object $parentClass
     * @param bool $instanciable
     * @return array
     * @throws InvalidArgumentException
     */
    public static function implements_class(string|object $parentClass, bool $instanciable = true): array
    {
        static $parsed = [
            'count' => 0,
            'classes' => []
        ];

        $cache = &$parsed['classes'];
        $count = &$parsed['count'];

        $instanciable = (int) $instanciable;

        if (is_object($parentClass)) {
            $parentClass = get_class($parentClass);
        }

        if ( ! class_exists($parentClass) && ! interface_exists($parentClass)) {
            throw new InvalidArgumentException(sprintf('Invalid class %s', $parentClass));
        }

        $iterator = array_reverse(get_declared_classes());

        if (count($iterator) !== $count) {
            $cache = [];
            $count = count($iterator);
        }

        if (isset($cache[$parentClass])) {
            return $cache[$parentClass][$instanciable];
        }

        $cache[$parentClass] = [[], []];

        $result = &$cache[$parentClass];

        $method = interface_exists($parentClass) ? 'class_implements' : 'class_parents';

        foreach ($iterator as $class) {
            if ($class === $parentClass) {
                continue;
            }

            if (in_array($parentClass, $method($class))) {
                $result[0][$class] = $class;
                if ((new \ReflectionClass($class))->isInstantiable()) {
                    $result[1][$class] = $class;
                }
            }
        }
        return $result[$instanciable];
    }

    /**
     * Uses callback for each elements of the array and returns the value
     *
     * @param callable $callback
     * @param iterable $iterable
     * @return iterable
     */
    public static function each(callable $callback, iterable $iterable): iterable
    {

        foreach ($iterable as $key => $value) {
            $result = $callback($value, $key, $iterable);
            yield $key => $result;
        }
    }

    /**
     * Filters elements of an iterable using a callback function
     *
     * @param callable $callback accepts $value, $key, $array
     * @param iterable $iterable
     * @return array
     */
    public static function filter(callable $callback, iterable $iterable): array
    {
        $new = [];

        foreach ($iterable as $key => $value) {
            if ( ! $callback($value, $key, $iterable)) {
                continue;
            }
            if (is_int($key) || is_null($key)) {
                $new[] = $value;
                continue;
            }
            $new[$key] = $value;
        }
        return $new;
    }

    /**
     * Same as the original except callback accepts more arguments and works with string keys
     * @param callable $callback accepts $value, $key, $array
     * @param iterable $iterable
     * @return array
     */
    public static function map(callable $callback, iterable $iterable): array
    {
        $new = [];
        foreach ($iterable as $key => $value) {

            // key can be passed by reference
            $result = $callback($value, $key, $iterable);

            //no return value? $value passed by reference?
            if ($result === null) {
                $result = $value;
            }

            if ( ! is_string($key)) {
                $new[] = $result;
                continue;
            }
            $new[$key] = $result;
        }
        return $new;
    }

    /**
     * Tests if at least one element in the iterable passes the test implemented by the provided function.
     * @param callable $callback
     * @param iterable $iterable
     * @return bool
     * @throws RuntimeException
     */
    public static function some(callable $callback, iterable $iterable): bool
    {
        foreach ($iterable as $key => $value) {
            if ( ! $callback($value, $key, $iterable)) {
                continue;
            }
            return true;
        }
        return false;
    }

    /**
     * Tests if all elements in the iterable pass the test implemented by the provided function.
     * @param callable $callback
     * @param iterable $iterable
     * @return bool
     * @throws RuntimeException
     */
    public static function every(callable $callback, iterable $iterable): bool
    {
        foreach ($iterable as $key => $value) {
            if ( ! $callback($value, $key, $iterable)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if is a valid url
     * @link https://gist.github.com/dperini/729294
     * @param string $url
     * @param bool $webonly Put local urls as invalid ( eg : "http://localhost/index.php" )
     * @return bool
     */
    public static function isValidUrl(string $url, bool $webonly = false): bool
    {
        return preg_match($webonly ? self::WEB_URL_REGEX : self::LOCAL_URL_REGEX, $url) > 0;
    }

    /**
     * Convert CamelCased to camel_cased
     * @param string $camelCased
     * @return string
     */
    public static function to_snake(string $camelCased): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($camelCased)));
    }

    /**
     * Convert snake_case to snakeCase
     * @param string $snake_case
     * @return string
     */
    public static function toCamelCase(string $snake_case): string
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) {
            return ('.' === $match[1] ? '_' : '') . strtoupper($match[2]);
        }, $snake_case);
    }

    /**
     * Return current Unix timestamp in milliseconds
     * @link https://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php
     * @return int
     */
    public static function millitime(): int
    {
        list($usec, $sec) = explode(' ', microtime());
        return (int) ((int) $sec * 1000 + ((float) $usec * 1000));
    }

    /**
     * Generates a uuid V4
     * @link https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     * @return string
     */
    public static function generate_uuid_v4(): string
    {
        if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Returns whether this string consists entirely of ASCII characters
     *
     * @param string $input
     * @return bool
     */
    public static function isAscii(string $input): bool
    {
        return \preg_match('/[^\x00-\x7F]/', $input) === 0;
    }

    /**
     * Returns whether this string consists entirely of printable ASCII characters
     *
     * @param string $input
     * @return bool
     */
    public static function isPrintableAscii(string $input): bool
    {
        return \preg_match('/[^\x20-\x7E]/', $input) === 0;
    }

    /**
     * Get Human Readable file size
     *
     * @link https://gist.github.com/liunian/9338301
     * @staticvar array $units
     * @param int|float $size
     * @param int $precision
     * @return string
     */
    public static function getFilesize(int|float $size, int $precision = 2): string
    {
        static $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;
        while (($size / $step) >= 1) {
            $size = $size / $step;
            $i ++;
        }
        return round($size, $precision) . $units[$i];
    }

    /**
     * Get script execution time
     *
     * @return float|int
     */
    public static function getExecutionTime(int $precision = 6): float|int
    {
        return round(microtime(true) - SCRIPT_START, $precision);
    }

    /**
     * Pauses script execution for a given amount of time
     * combines sleep or usleep
     *
     * @param int|float $seconds
     */
    public static function pause(int|float $seconds): void
    {
        /**
         * @link https://www.php.net/manual/en/function.usleep.php
         * Note: Values larger than 1000000 (i.e. sleeping for more than a second) may not be supported by the operating system.
         * Use sleep() instead.
         */
        if ($seconds <= 0) {
            return;
        }

        $iseconds = intval(floor($seconds));
        $seconds -= $iseconds;
        $microseconds = (int) round($seconds / static::MICROSECOND);

        if ($iseconds > 0) {
            sleep($iseconds);
        }

        if ($microseconds > 0) {
            usleep((int) round($seconds / static::MICROSECOND));
        }
    }

    /**
     * Pauses script execution for a given amount of milliseconds
     *
     * @param int $milliseconds
     * @return void
     */
    public static function msleep(int $milliseconds): void
    {
        static::pause($milliseconds * static::MILLISECOND);
    }

    /**
     * Get Constants defined in a class
     *
     * @phan-suppress PhanParamTooManyInternal
     * @param string|object $class
     * @param bool $public if True returns only public visibility constants
     * @return array
     */
    public static function getClassConstants(string|object $class, bool $public = true): array
    {
        static $parsed = [
            [], []
        ];

        if (is_object($class)) {
            $class = get_class($class);
        }

        if ( ! class_exists($class) && ! interface_exists($class)) {
            return [];
        }

        $cache = &$parsed[(int) $public];

        if ( ! isset($cache[$class])) {

            try {
                $result = [];
                $filter = $public ? ReflectionClassConstant::IS_PUBLIC : null;
                foreach (array_reverse(class_parents($class) ?: []) + [$class => $class] as $className) {
                    $reflector = new ReflectionClass($className);
                    foreach ($reflector->getConstants($filter) as $constant => $value) {
                        if (array_key_exists($constant, $result)) {
                            continue;
                        }
                        $result[$constant] = $value;
                    }
                }

                $cache[$class] = $result;
            } catch (ReflectionException) {
                return $cache[$class] = [];
            }
        }
        return $cache[$class];
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     */
    public static function randomString(int $length = 16): string
    {

        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

}
