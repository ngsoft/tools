<?php

declare(strict_types=1);

namespace NGSOFT;

use ErrorException,
    FilesystemIterator,
    Generator,
    InvalidArgumentException,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator,
    ReflectionClass,
    ReflectionClassConstant,
    ReflectionException,
    RuntimeException,
    SplFileInfo;
use const SCRIPT_START;
use function str_ends_with,
             str_starts_with;

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
     * @suppress PhanTypeMismatchArgumentInternal
     * @staticvar Closure $handler
     * @param callable $callback
     * @param mixed ...$args args to be passed to the callback
     * @return mixed
     */
    public static function safe_exec(callable $callback, ...$args)
    {
        static $handler;
        if ( ! $handler) {
            $handler = static function () {

            };
        }
        set_error_handler($handler);
        $retval = null;
        try {
            $retval = call_user_func_array($callback, $args);
        } finally { restore_error_handler(); }
        return $retval;
    }

    /**
     * Convenient Function used to convert php errors, warning, ... as Throwable
     * @return callable|null
     */
    public static function errors_as_exceptions()
    {

        return set_error_handler(function ($type, $msg, $file, $line) {
            if ( ! (error_reporting() & $type)) return false;
            throw new ErrorException($msg, 0, $type, $file, $line);
        });
    }

    /**
     * Normalize pathnames
     *
     * @param string $path
     * @return string
     */
    public static function normalize_path(string $path): string
    {
        return preg_replace('#[\\\/]+#', '/', $path);
    }

    /**
     * Change the current active directory
     * And stores the last position, use popd() to return to previous directory
     * @param string $dir
     * @return bool
     */
    public static function pushd(string $dir): bool
    {

        $current = getcwd();
        if (
                is_dir($dir) &&
                chdir($dir)
        ) {
            if ($current !== false) {
                self::$pushd_history[] = $current;
            }
            return true;
        }
        return false;
    }

    /**
     * Restore the last active directory changed by pushd
     * @return string|false current directory
     */
    public static function popd(): string|false
    {
        $previous = array_pop(self::$pushd_history) ?? getcwd();
        $previous && is_dir($previous) && chdir($previous);
        return getcwd();
    }

    /**
     * List regular files in a given directory
     * @param string $dir Directory to list
     * @param string ...$extensions extensions to list (or all files if empty)
     * @return string[]
     */
    public static function listFiles(string $dir, bool $recursive = false, string ...$extensions): array
    {
        $dir = realpath($dir);
        $result = [];
        if (
                $dir !== false and
                is_dir($dir)
        ) {
            $hasExtensions = count($extensions) > 0;
            foreach (scandir($dir) as $file) {
                if (
                        $file === '.' ||
                        $file === '..'
                ) continue;

                if ($path = realpath($dir . DIRECTORY_SEPARATOR . $file)) {

                    if (is_dir($path)) {
                        if ( ! $recursive) continue;
                        $result = array_merge($result, self::listFiles($path, $recursive, ...$extensions));
                    } elseif (is_file($path)) {
                        if ( ! $hasExtensions || self::fileHasExtension($path, ...$extensions)) $result[] = $path;
                    }
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * Get Recursive Dir Iterator
     * @param string $dir
     * @return Generator<string,SplFileInfo>
     */
    private static function getDirectoryIterator(string $dir): Generator
    {

        if ( ! is_dir($dir)) throw new InvalidArgumentException(sprintf('%s is not a directory.', $dir));
        yield from (new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
        ));
    }

    /**
     * Get a Recursive Directory Iterator that list all the files
     *
     * @param string $dir Directory to scan
     * @param string ...$extensions if at least one extension is set the iterator will only list files with that/those extension(s)
     * @return Generator<string,SplFileInfo> An Iterator of SplFileInfo or empty if directory does not exists
     */
    public static function getRecursiveDirectoryIterator(string $dir, string ...$extensions): Generator
    {

        $findExtensions = count($extensions) > 0;
        /** @var SplFileInfo $fileInfo */
        foreach (self::getDirectoryIterator($dir) as $fileName => $fileInfo) {

            if ($fileInfo->isDir()) continue;

            if ( ! $findExtensions || self::fileHasExtension($fileName, ...$extensions)) {
                yield $fileName => $fileInfo;
            }
        }
    }

    /**
     * Checks whenever the filename has the given extension(s)
     *
     * @param string $filename
     * @param string ...$extensions
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function fileHasExtension(string $filename, string ...$extensions): bool
    {
        if (count($extensions) === 0) {
            throw new InvalidArgumentException('You need to specify at least one extention to match.');
        }

        $fileCheck = strtolower($filename);
        foreach ($extensions as $extension) {
            $extCheck = strtolower(str_starts_with($extension, '.') ? $extension : ".$extension");
            if (str_ends_with($fileCheck, $extCheck)) return true;
        }
        return false;
    }

    /**
     * Wrap include outside of the global scope
     * @staticvar callable $callback
     * @param string $filename
     * @param bool $once
     * @param array $data Data to extract as $data['key'] => $key in the file
     * @return mixed
     */
    public static function include(string $filename, bool $once = true, array $data = []): mixed
    {
        static $callback;
        if ( ! $callback) {

            $callback = static function (array $___data) {
                extract($___data);
                return func_get_arg(2) ?
                include_once func_get_arg(1) :
                include func_get_arg(1);
            };
        }


        $real = realpath($filename);

        return ($real !== false and is_file($real)) ?
                call_user_func_array($callback, [$data, $real, $once]) :
                null;
    }

    /**
     * Loads all php files found recursively
     * @param string $dir directory to load
     * @param array $data data to import
     * @param string $ext PHP file extension to use
     * @return void
     */
    public static function autoload(string $dir, array $data = [], string $ext = 'php'): void
    {

        self::errors_as_exceptions();
        try {
            /** @var SplFileInfo $fileInfo */
            foreach (self::getRecursiveDirectoryIterator($dir, $ext) as $phpFile => $fileInfo) {

                self::include($phpFile, true, $data);
            }
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Find ClassList extending or implementing a parent Class, Interface, or using Trait
     * Classes to find must be loaded first. Tools::autoload()
     * @staticvar int $classcount get the last classcount for cache use
     * @staticvar array $cache keep the last results and returns them if $classcount has not changed
     * @param string $parentClass
     * @param bool $onlyInstanciable
     * @return array
     * @throws InvalidArgumentException
     */
    public static function getClassesImplementing(string $parentClass, bool $onlyInstanciable = true): array
    {

        static $classcount, $cache;
        $classcount = $classcount ?? 0;

        if (
                ! class_exists($parentClass) and
                ! interface_exists($parentClass) and
                ! trait_exists($parentClass)
        ) throw new InvalidArgumentException(sprintf('Invalid class name "%s"', $parentClass));


        $reflector = new ReflectionClass($parentClass);
        $method = 'class_parents';
        if ($reflector->isInterface()) $method = 'class_implements';
        elseif ($reflector->isTrait()) $method = 'class_uses';

        if ($classlist = array_reverse(get_declared_classes())) {

            if ($classcount != count($classlist)) {
                $cache = [];
                $classcount = count($classlist);
            }

            $key = $onlyInstanciable ? 'instanciable' : 'all';

            if (isset($cache[$parentClass])) return $cache[$parentClass][$key];


            $cache[$parentClass] = [
                'instanciable' => [], 'all' => []
            ];

            foreach ($classlist as $className) {
                if ($className == $parentClass) continue;

                if (
                        in_array($parentClass, $method($className))
                ) {
                    $cache[$parentClass]['all'][] = $className;
                    if ((new ReflectionClass($className))->isInstantiable()) {
                        $cache[$parentClass]['instanciable'][] = $className;
                    }
                }
            }

            return $cache[$parentClass][$key];
        }
        //will probably never happen
        return [];
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

            if (is_int($key) || is_null($key)) {
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
     * uses sleep or usleep
     *
     * @param int|float $seconds
     */
    public static function pause(int|float $seconds): void
    {

        if (is_int($seconds)) {
            sleep($seconds);
            return;
        }

        usleep((int) round($seconds * 1e+6));
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

}
