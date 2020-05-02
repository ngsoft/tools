<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ErrorException,
    JsonSerializable,
    ReflectionClass,
    stdClass;
use const day,
          hour,
          localurl,
          minute,
          weburl,
          year;
use function mb_internal_encoding,
             mb_stripos,
             mb_strpos;

////////////////////////////   Version   ////////////////////////////
define('NGSOFT\\Tools\\VERSION', '1.3.1');

////////////////////////////   Error Handler   ////////////////////////////
/**
 * A function that does noop
 * @return void
 */
function noop() { return; }

/**
 * Execute a callback and hides all errors that can be thrown
 * @param callable $callback
 * @param mixed ...$args args to be passed to the callback
 * @return mixed
 */
function safe_exec(callable $callback, ...$args) {
    \set_error_handler('NGSOFT\Tools\noop');
    $retval = call_user_func_array($callback, $args);
    \restore_error_handler();
    return $retval;
}

/**
 * Convenient Function used to convert php errors, warning, ... as Throwable
 * @return callable|null
 */
function errors_as_exceptions() {
    return \set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        if (!error_reporting()) return false;
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });
}

////////////////////////////   Some Helpers   ////////////////////////////

mb_internal_encoding("UTF-8");

@define('ds', DIRECTORY_SEPARATOR);
@define('ns', "\\");
@define('dot', '.');
@define('eol', PHP_EOL);
@define('minute', 60);
@define('hour', minute * 60);
@define('day', hour * 24);
@define('week', day * 7);
@define('year', 365 * day);
@define('month', round(year / 12, 0));
@define('KB', 1024);
@define('MB', KB * 1024);
@define('GB', MB * 1024);
@define('TB', GB * 1024);
//@define('now', time());

/**
 * @link https://gist.github.com/dperini/729294 (with protocol required)
 * original:
 * /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[\/?#]\S*)?$/iu
 */
@define('weburl', '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[\/?#]\S*)?$/iu');
@define('localurl', '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:\.?[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}])+(?:(?:\.[a-z\x{00a1}-\x{ffff}]{2,})\.?)?)(?::\d{2,5})?(?:[\/?#]\S*)?$/iu');

////////////////////////////   File Operations   ////////////////////////////

/**
 * Normalize pathnames
 * @param string $path
 * @return string
 */
function normalizePath(string $path): string {
    return preg_replace('/[\\\\\/]+/', '/', $path);
}

/**
 * Change the current active directory
 * @global array $pushd
 * @param string $dir
 * @return boolean
 */
function pushd(string $dir): bool {
    global $pushd;
    if (is_null($pushd)) $pushd = [];
    if (is_dir($dir)) {
        $pushd[] = getcwd();
        return chdir($dir);
    }
    return false;
}

/**
 * Restore the last active directory changed by pushd
 * @global array $pushd
 * @return boolean
 */
function popd(): bool {
    global $pushd;
    $last = is_array($pushd) ? array_pop($pushd) : false;
    if (is_dir($last)) return chdir($last);
    return false;
}

/**
 * Wrap include outside of the global scope
 * @param string $file file to include
 * @param array $data data to pass to the file
 * @param bool $once include_once
 * @return mixed|null
 */
function safeInclude(string $file, array $data = [], bool $once = false) {
    $file = realpath($file);
    if ($file !== false && is_file($file)) {
        extract($data);
        if ($once === true) return include_once $file;
        return include $file;
    }
    return null;
}

/**
 * List files in a given directory
 * @param string $dir Directory to list
 * @param string ...$extensions extensions to list
 * @return array
 */
function listFiles(string $dir, string ...$extensions): array {
    $list = [];
    $dir = realpath($dir);
    if ($dir !== false && is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if ($file[0] === '.') continue;
            if (count($extensions) > 0) {
                foreach ($extensions as $extension) {
                    $match = ".$extension";
                    if (substr_compare($file, $match, -strlen($match)) === 0) $list[] = realpath("$dir/$file");
                }
            } else $list[] = realpath("$dir/$file");
        }
    }
    return $list;
}

/**
 * Loads all .php files found recursively
 * @param string $path
 * @param array $data
 * @return void
 */
function autoload(string $path, array $data = []): void {
    foreach (listFiles($path, 'php') as $phpfile) safeInclude($phpfile, $data, true);
}

/**
 * Recursively remove directory
 * @param string $src
 * @return bool
 */
function rrmdir(string $src): bool {
    $src = realpath($src);
    if ($src === false || !is_dir($src)) return false;
    $dir = opendir($src);
    while (false !== ( $file = readdir($dir))) {
        if ($file[0] === ".") continue;
        $path = $src . DIRECTORY_SEPARATOR . $file;
        is_dir($path) ? rrmdir($path) : unlink($path);
    }
    closedir($dir);
    return rmdir($src);
}

////////////////////////////   OOP   ////////////////////////////

/**
 * Find ClassList extending or implementing a parent Class
 * @param string $parentClass An Interface or an extended class
 * @return array<int, string> A list of class extending or implementing given class that are not abstract, traits, or interfaces
 */
function class_children(string $parentClass): array {
    $result = [];
    if (
            (class_exists($parentClass) or interface_exists($parentClass))
            and $classList = array_reverse(get_declared_classes())
    ) {
        foreach ($classList as $class) {
            $reflect = new ReflectionClass($class);
            if ($reflect->isAbstract() or $reflect->isTrait() or $reflect->isInterface()) continue;
            if ($reflect->isSubclassOf($parentClass)) $result[] = $class;
        }
    }
    return $result;
}

////////////////////////////   Arrays   ////////////////////////////

/**
 * Convert an object to array
 * @param JsonSerializable|stdClass $object
 * @return array|null
 */
function object_to_array(object $object) {
    if (
            ($object instanceof JsonSerializable)
            or ( $object instanceof stdClass)
    ) {
        $json = json_encode($object);
        return json_decode($json, true);
    }
    return null;
}

/**
 * Convert array to object
 * @param array $array
 * @return stdClass|array<stdClass>|null
 */
function array_to_object(array $array) {
    if ($json = json_encode($array)) {
        return json_decode($json);
    }
    return null;
}

/**
 * Same as the original except callback accepts more arguments
 * @param callable $callback
 * @param array $array
 * @return array
 */
function _array_map(callable $callback, array $array): array {
    $new = [];
    foreach ($array as $k => $v) {
        $new[$k] = $callback($v, $k, $array);
    }
    return $new;
}

/**
 * Tests if all elements in the array pass the test implemented by the provided function.
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_every(callable $callback, array $array): bool {
    foreach ($array as $k => $v) {
        if (true !== $callback($v, $k, $array)) return false;
    }
    return true;
}

/**
 * A foreach loop for an array
 * @param callable $callback
 * @param array $array
 * @return void
 */
function array_foreach(callable $callback, array $array): void {
    foreach ($array as $k => $v) {
        $callback($v, $k, $array);
    }
}

/**
 * Tests if at least one element in the array passes the test implemented by the provided function..
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_some(callable $callback, array $array): bool {
    foreach ($array as $k => $v) {
        if (true === $callback($v, $k, $array)) return true;
    }
    return false;
}

/**
 * Flatten  a multi-dimensional array and preserves keys for non numeric ones
 * @param array $array
 * @param int $depth
 * @return array
 */
function array_flatten(array $array, int $depth = 1) {
    $result = [];
    if ($depth === 0) return $array;
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $kk => $vv) {
                if (is_array($vv) and $depth > 0) {
                    $result = array_merge($result, array_flatten($vv, $depth - 1));
                    continue;
                }
                is_int($kk) ? $result[] = $vv : $result[$kk] = $vv;
            }
        } else is_int($k) ? $result[] = $v : $result[$k] = $v;
    }
    return $result;
}

/**
 * array_replace_recursive that appends numeric keys instead of replacing them
 * @param array $orig
 * @param array ...$arrays
 * @return array
 */
function array_concat(array $orig, array ...$arrays): array {
    $result = $orig;
    foreach ($arrays as $n => $array) {
        if (assert(is_array($array), sprintf("Expected parameter %d to be an array.", $n + 2))) {
            foreach ($array as $key => $value) {
                if (
                        is_array($value) &&
                        isset($result[$key]) &&
                        is_array($result[$key])
                ) $result[$key] = array_concat($result[$key], $value);
                elseif (is_int($key)) $result[] = $value;
                else $result [$key] = $value;
            }
        }
    }
    return $result;
}

////////////////////////////   Strings   ////////////////////////////

/**
 * Checks if haystack begins with needle
 * @link https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function startsWith(string $haystack, string $needle): bool {
    return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}

/**
 * Checks if haystack ends with needle
 * @link https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function endsWith(string $haystack, string $needle): bool {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

/**
 * Checks whenever $haystack contains $needle
 * @param string $haystack The string being checked.
 * @param string $needle The string to find in haystack
 * @param bool $insensitive
 * @return bool
 */
function contains(string $haystack, string $needle, bool $insensitive = false): bool {
    return $insensitive ? mb_stripos($haystack, $needle) !== false : mb_strpos($haystack, $needle) !== false;
}

/**
 * Convert CamelCased to camel_cased
 * @param string $camelCased
 * @return string
 */
function toSnake(string $camelCased): string {
    return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($camelCased)));
}

/**
 * Convert snake_case to SnakeCase
 * @param string $snake_case
 * @return string
 */
function toCamelCase(string $snake_case): string {
    return preg_replace_callback('/(^|_|\.|\-)+(.)/', function ($match) {
        return strtoupper($match[2]);
    }, $snake_case);
}

/**
 * Perform a regular expression match
 * @param string $pattern The pattern to search for, as a string.
 * @param string $subject The input string
 * @param bool $global Perform a global regular expression match instead
 * @return array
 */
function match(string $pattern, string $subject, bool $global = false): array {
    return $global ? (preg_match_all($pattern, $subject, $matches) > 0 ? $matches : []) : (preg_match($pattern, $subject, $matches) > 0 ? $matches : []);
}

/**
 * Checks if is a valid url
 * @link https://gist.github.com/dperini/729294
 * @param string $url
 * @param bool $webonly Put local urls as invalid ( eg : "http://localhost/index.php" )
 * @return bool
 */
function validUrl(string $url, bool $webonly = false) {
    return preg_match($webonly === true ? weburl : localurl, $url) > 0;
}
