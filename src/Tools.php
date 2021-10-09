<?php

declare(strict_types=1);

namespace NGSOFT;

use EmptyIterator,
    ErrorException,
    FilesystemIterator,
    InvalidArgumentException,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator,
    ReflectionClass,
    RuntimeException,
    SplFileInfo,
    Traversable;
use function mb_strrpos,
             mb_strtolower,
             mb_substr,
             str_starts_with;

/**
 * Useful Functions to use in my projects
 */
final class Tools {

    /**
     * Package Version Information
     */
    public const VERSION = '2.1';

    /**
     * URL Parser Regex
     * @link https://gist.github.com/dperini/729294 (with protocol required)
     */
    public const WEB_URL_REGEX = '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[\/?#]\S*)?$/iu';
    public const LOCAL_URL_REGEX = '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:\.?[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}])+(?:(?:\.[a-z\x{00a1}-\x{ffff}]{2,})\.?)?)(?::\d{2,5})?(?:[\/?#]\S*)?$/iu';

    /**
     * Time Constants (in seconds)
     */
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
    public static function safe_exec(callable $callback, ...$args) {
        static $handler;
        if (!$handler) {
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
    public static function errors_as_exceptions() {

        return set_error_handler(function ($type, $msg, $file, $line) {
            if (!error_reporting()) return false;
            throw new ErrorException($msg, 0, $type, $file, $line);
        });
    }

    /**
     * Normalize pathnames
     *
     * @param string $path
     * @return string
     */
    public static function normalize_path(string $path): string {
        return preg_replace('#[\\\/]+#', '/', $path);
    }

    /**
     * Change the current active directory
     * And stores the last position, use popd() to return to previous directory
     * @param string $dir
     * @return bool
     */
    public static function pushd(string $dir): bool {

        $current = getcwd();
        if (
                is_dir($dir) and
                chdir($dir)
        ) {
            self::$pushd_history[] = $current;
            return true;
        }
        return false;
    }

    /**
     * Restore the last active directory changed by pushd
     * @return bool
     */
    public static function popd(): bool {
        $previous = array_pop(self::$pushd_history) ?? getcwd();
        return is_dir($previous) and chdir($previous);
    }

    /**
     * List regular files in a given directory
     * @param string $dir Directory to list
     * @param string ...$extensions extensions to list (or all files if empty)
     * @return string[]
     */
    public static function listFiles(string $dir, bool $recursive = false, string ...$extensions): array {
        $dir = realpath($dir);
        if (
                $dir !== false and
                is_dir($dir)
        ) {
            $result = [];
            $exts = array_map(fn($ext) => mb_strtolower(str_starts_with($ext, '.') ? $ext : ".$ext"), $extensions);
            $extCount = count($extensions);
            foreach (scandir($dir) as $file) {
                if (
                        $file == '.' or
                        $file == '..'
                ) continue;

                if ($path = realpath($dir . DIRECTORY_SEPARATOR . $file)) {

                    if (
                            is_file($path) and
                            $extCount > 0 ?
                                    ( $fileExt = ($pos = mb_strrpos($file, '.')) !== false ?
                                    mb_strtolower(mb_substr($file, $pos)) :
                                    null and in_array($fileExt, $exts) ) :
                                    true
                    ) $result[] = $path;
                    elseif (
                            is_dir($path) and
                            $recursive
                    ) $result = array_merge($result, self::listFiles($path, $recursive, ...$extensions));
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * Get Recursive Dir Iterator
     * @param string $dir
     * @return Traversable
     */
    private static function getDirectoryIterator(string $dir): Traversable {
        return
                is_dir($dir) ?
                new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                ) :
                new EmptyIterator();
    }

    /**
     * Get a Recursive Directory Iterator that list all the files
     * If no extension provided will list all files (including directories)
     * listFiles does the same but without iterator
     *
     * @param string $dir Directory to scan
     * @param string ...$extensions if at least one extension is set the iterator will only list files with that/those extension(s)
     * @return SplFileInfo[]|Traversable An Iterator of SplFileInfo or empty if directory does not exists
     */
    public static function getRecursiveDirectoryIterator(string $dir, string ...$extensions) {
        /** @var SplFileInfo $file */
        foreach (self::getDirectoryIterator($dir) as $name => $file) {
            if (
                    !$extensions or
                    (!$file->isDir() and self::fileHasExtension($name, ...$extensions))
            ) {
                // yield in function === returns a generator(even if return is before !)
                yield $name => $file;
            }
        }
    }

    /**
     * Checks whenever the filename has the given extension(s)
     * @param string $filename Filename to test
     * @param string $extension Extension to match against
     * @param string ...$additionalExtensions More extensions to match
     * @return bool
     */
    public static function fileHasExtension(string $filename, string $extension, string ...$additionalExtensions): bool {

        $fileExt = ($pos = mb_strrpos($filename, '.')) !== false ?
                mb_strtolower(mb_substr($filename, $pos)) :
                null;

        return $fileExt !== null and
                in_array($fileExt,
                        array_map(fn($ext) => mb_strtolower(str_starts_with($ext, '.') ? $ext : ".$ext"),
                                array_merge([$extension], $additionalExtensions)));
    }

    /**
     * Wrap include outside of the global scope
     * @staticvar callable $callback
     * @param string $filename
     * @param bool $once
     * @param array $data Data to extract as $data['key'] => $key in the file
     * @return mixed|null
     */
    public static function include(string $filename, bool $once = true, array $data = []) {
        static $callback;
        if (!$callback) {

            $callback = static function (array $data) {
                extract($data);
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
    public static function autoload(string $dir, array $data = [], string $ext = 'php'): void {

        self::errors_as_exceptions();
        try {
            foreach (self::listFiles($dir, true, $ext)as $phpfile) self::include($phpfile, true, $data);
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
     * @return array
     * @throws InvalidArgumentException
     */
    public static function getClassesImplementing(string $parentClass): array {

        static $classcount, $cache;
        $classcount = $classcount ?? 0;

        if (
                !class_exists($parentClass) and
                !interface_exists($parentClass) and
                !trait_exists($parentClass)
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

            if (isset($cache[$parentClass])) return $cache[$parentClass];

            $cache[$parentClass] = [];

            foreach ($classlist as $className) {
                if ($className == $parentClass) continue;

                if (
                        in_array($parentClass, $method($className)) and
                        (new ReflectionClass($className))->isInstantiable()
                ) {
                    $cache[$parentClass][] = $className;
                }
            }

            return $cache[$parentClass];
        }
        //will probably never happen
        return [];
    }

    /**
     * Same as the original except callback accepts more arguments and works with string keys
     * @param callable $callback accepts $value, $key, $array
     * @param iterable $array
     * @return array
     */
    public static function array_map(callable $callback, iterable $array): array {
        $new = [];
        foreach ($array as $key => $value) {
            $new[$key] = $callback($value, $key, $array);
        }
        return $new;
    }

    /**
     * Tests if at least one element in the iterable passes the test implemented by the provided function.
     * @param callable $callback
     * @param iterable $array
     * @return bool
     * @throws RuntimeException
     */
    public static function array_some(callable $callback, iterable $array): bool {
        foreach ($array as $key => $value) {
            $result = $callback($value, $key, $array);
            if (!is_bool($result)) throw new RuntimeException('Callback callable must return a boolean.');
            if ($result === true) return true;
        }
        return false;
    }

    /**
     * Tests if all elements in the iterable pass the test implemented by the provided function.
     * @param callable $callback
     * @param iterable $array
     * @return bool
     * @throws RuntimeException
     */
    public static function array_every(callable $callback, iterable $array): bool {
        foreach ($array as $key => $value) {
            $result = $callback($value, $key, $array);
            if (!is_bool($result)) throw new RuntimeException('Callback callable must return a boolean.');
            if ($result === false) return false;
        }
        return true;
    }

    /**
     * Convert iterable to array recursively
     * @param iterable $obj
     * @return array
     */
    public static function iterable_to_array(iterable $obj): array {
        $result = [];
        foreach ($obj as $key => $value) {
            if (is_iterable($value)) {
                $result[$key] = self::iterable_to_array($value);
            } else $result[$key] = $value;
        }
        return $result;
    }

    /**
     * array_replace_recursive that appends numeric keys instead of replacing them
     * @param iterable $source
     * @param iterable $array
     * @return array
     */
    private static function array_merge(iterable $source, iterable $array): array {
        $result = self::iterable_to_array($source);
        foreach ($array as $key => $value) {
            if (is_int($key)) $result[] = $value;
            elseif (is_iterable($value)) {
                if (
                        array_key_exists($key, $result) and
                        is_array($result[$key])
                ) $result[$key] = self::array_merge($result[$key], $value);
                else $result[$key] = self::iterable_to_array($value);
            } else $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Merges multiples iterables
     *
     * @param iterable ...$iterables one to many iterables
     * @return array
     */
    public static function array_concat(iterable ...$iterables): array {
        $result = [];
        foreach ($iterables as $iterable) {
            $result = self::array_merge($result, $iterable);
        }
        return $result;
    }

    /**
     * Checks if is a valid url
     * @link https://gist.github.com/dperini/729294
     * @param string $url
     * @param bool $webonly Put local urls as invalid ( eg : "http://localhost/index.php" )
     * @return bool
     */
    public static function isValidUrl(string $url, bool $webonly = false): bool {
        return preg_match($webonly ? self::WEB_URL_REGEX : self::LOCAL_URL_REGEX, $url) > 0;
    }

    /**
     * Convert CamelCased to camel_cased
     * @param string $camelCased
     * @return string
     */
    public static function to_snake(string $camelCased): string {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($camelCased)));
    }

    /**
     * Convert snake_case to snakeCase
     * @param string $snake_case
     * @return string
     */
    public static function toCamelCase(string $snake_case): string {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) {
            return ('.' === $match[1] ? '_' : '') . strtoupper($match[2]);
        }, $snake_case);
    }

    /**
     * Return current Unix timestamp in milliseconds
     * @link https://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php
     * @return int
     */
    public static function millitime(): int {
        list($usec, $sec) = explode(' ', microtime());
        return (int) ((int) $sec * 1000 + ((float) $usec * 1000));
    }

    /**
     * Generates a uuid V4
     * @link https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     * @return string
     */
    public static function generate_uuid_v4(): string {
        if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}
