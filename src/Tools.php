<?php

declare(strict_types=1);

namespace NGSOFT;

/**
 * Useful Functions to use in my projects.
 */
final class Tools
{
    /**
     * Package Version Information.
     */
    public const VERSION         = '4.1.0';

    /**
     * URL Parser Regex.
     *
     * @see https://gist.github.com/dperini/729294 (with protocol required)
     */
    public const WEB_URL_REGEX   = '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[\/?#]\S*)?$/iu';
    public const LOCAL_URL_REGEX = '/^(?:(?:(?:https?|ftp):)\/\/)(?:\S+(?::\S*)?@)?(?:(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:\.?[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}])+(?:(?:\.[a-z\x{00a1}-\x{ffff}]{2,})\.?)?)(?::\d{2,5})?(?:[\/?#]\S*)?$/iu';

    /**
     * Time Constants (in seconds).
     */
    public const MICROSECOND     = 1e-6;
    public const MILLISECOND     = 1e-3;
    public const SECOND          = 1;
    public const MINUTE          = 60;
    public const HOUR            = 3600;
    public const DAY             = 86400;
    public const WEEK            = 604800;
    public const MONTH           = 2628000;
    public const YEAR            = 31536000;

    /**
     * Size Constants
     * in bytes.
     */
    public const KB              = 1024;
    public const MB              = 1048576;
    public const GB              = 1073741824;
    public const TB              = 1099511627776;

    // //////////////////////////   Error Handling   ////////////////////////////

    /**
     * Execute a callback and hides all php errors that can be thrown
     * Exceptions thrown inside the callback will be preserved.
     */
    public static function safe_exec(callable $callback, mixed ...$args): mixed
    {
        try
        {
            self::suppress_errors();
            return $callback(...$args);
        } finally
        {
            restore_error_handler();
        }
    }

    /**
     * Convenient Function used to convert php errors, warning, ... as Throwable.
     */
    public static function errors_as_exceptions(): null|callable
    {
        return \set_default_error_handler();
    }

    /**
     * Set error handler to empty closure (as of php 8.1 @ doesn't work anymore).
     *
     * @phan-suppress PhanTypeMismatchArgumentInternal
     */
    public static function suppress_errors(): null|callable
    {
        static $handler;
        $handler ??= static fn () => null;
        return set_error_handler($handler);
    }

    // //////////////////////////   Files   ////////////////////////////

    /**
     * Normalize pathname.
     */
    public static function normalize_path(string $path): string
    {
        if (empty($path))
        {
            return $path;
        }

        $path = preg_replace('#[\\\/]+#', DIRECTORY_SEPARATOR, $path);

        if (in_array($path[-1], ['\\', '/']))
        {
            $path = \mb_substr($path, 0, -1);
        }

        return $path;
    }

    // //////////////////////////   Iterables   ////////////////////////////

    /**
     * Uses callback for each element of the array and returns the value.
     */
    public static function each(callable $callback, iterable $iterable): iterable
    {
        foreach ($iterable as $key => $value)
        {
            $result = $callback($value, $key, $iterable);
            yield $key => $result;
        }
    }

    /**
     * Iterate iterable.
     */
    public static function iterateAll(iterable $iterable): array
    {
        $result = [];

        foreach ($iterable as $index => $value)
        {
            if ( ! is_string($index))
            {
                $result[] = $value;
                continue;
            }
            $result[$index] = $value;
        }

        return $result;
    }

    /**
     * Filters elements of an iterable using a callback function.
     *
     * @param callable $callback accepts $value, $key, $array
     */
    public static function filter(callable $callback, iterable $iterable): array
    {
        $new = [];

        foreach ($iterable as $key => $value)
        {
            if ( ! $callback($value, $key, $iterable))
            {
                continue;
            }

            if ( ! is_string($key))
            {
                $new[] = $value;
                continue;
            }
            $new[$key] = $value;
        }
        return $new;
    }

    /**
     * Searches an iterable until element is found.
     *
     * @return null|mixed
     */
    public static function search(callable $callback, iterable $iterable): mixed
    {
        foreach ($iterable as $key => $value)
        {
            if ($callback($value, $key, $iterable))
            {
                return $value;
            }
        }
        return null;
    }

    /**
     * Same as the original except callback accepts more arguments and works with string keys.
     *
     * @param callable $callback accepts $value, $key, $array
     */
    public static function map(callable $callback, iterable $iterable): array
    {
        $new = [];

        foreach ($iterable as $key => $value)
        {
            // key can be passed by reference
            $result    = $callback($value, $key, $iterable);

            // no return value? $value passed by reference?
            if (null === $result)
            {
                $result = $value;
            }

            if ( ! is_string($key))
            {
                $new[] = $result;
                continue;
            }
            $new[$key] = $result;
        }
        return $new;
    }

    /**
     * Tests if at least one element in the iterable passes the test implemented by the provided function.
     *
     * @throws \RuntimeException
     */
    public static function some(callable $callback, iterable $iterable): bool
    {
        foreach ($iterable as $key => $value)
        {
            if ( ! $callback($value, $key, $iterable))
            {
                continue;
            }
            return true;
        }
        return false;
    }

    /**
     * Tests if all elements in the iterable pass the test implemented by the provided function.
     *
     * @throws \RuntimeException
     */
    public static function every(callable $callback, iterable $iterable): bool
    {
        foreach ($iterable as $key => $value)
        {
            if ( ! $callback($value, $key, $iterable))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Get a value(s) from the array, and remove it.
     */
    public static function pull(int|iterable|string $keys, array|\ArrayAccess &$iterable): mixed
    {
        if (is_iterable($keys))
        {
            $result = [];

            foreach ($keys as $key)
            {
                if (is_iterable($key))
                {
                    $result += self::pull($key, $iterable);
                    continue;
                }

                $result[] = self::pull($key, $iterable);
            }
            return $result;
        }

        $result = $iterable[$keys] ?? null;
        unset($iterable[$keys]);
        return $result;
    }

    /**
     * Clone all objects of an array recursively.
     */
    public static function cloneArray(array $array, bool $recursive = true): array
    {
        $result = [];

        foreach ($array as $offset => $value)
        {
            if (is_object($value))
            {
                $result[$offset] = clone $value;

                continue;
            }

            if (is_array($value) && $recursive)
            {
                $result[$offset] = self::cloneArray($value, $recursive);
                continue;
            }

            $result[$offset] = $value;
        }

        return $result;
    }

    /**
     * Converts an iterable to an array recursively
     * if the keys are not string they will be indexed.
     */
    public static function iterableToArray(iterable $iterable): array
    {
        $new = [];

        foreach ($iterable as $key => $value)
        {
            if (is_iterable($value))
            {
                $value = self::iterableToArray($value);
            }

            if ( ! is_string($key))
            {
                $new[] = $value;
                continue;
            }

            $new[$key] = $value;
        }

        return $new;
    }

    /**
     * Concatenate multiple values into the iterable provided recursively
     * If a provided value is iterable it will be merged into the iterable
     * (non-numeric keys will be replaced if not iterable into the provided object).
     */
    public static function concat(array|\ArrayAccess &$iterable, mixed ...$values): array|\ArrayAccess
    {
        static $check;
        $check ??= static function (mixed $value): bool
        {
            return is_array($value) || $value instanceof \ArrayAccess;
        };

        foreach ($values as $value)
        {
            if (is_iterable($value))
            {
                foreach ($value as $_key => $_value)
                {
                    if ( ! is_string($_key) && is_array($iterable))
                    {
                        $iterable[] = $_value;
                        continue;
                    }

                    if (is_int($_key))
                    {
                        $iterable[] = $_value;
                        continue;
                    }

                    // merge iterable together
                    if ($check($iterable[$_key]) && is_iterable($_value))
                    {
                        $iterable[$_key] = self::concat($iterable[$_key], $_value);
                        continue;
                    }

                    $iterable[$_key] = $_value;
                }
                continue;
            }

            $iterable[] = $value;
        }

        return $iterable;
    }

    /**
     * Count number of occurrences of value.
     */
    public static function countValue(mixed $value, iterable $iterable): int
    {
        $count = 0;

        foreach ($iterable as $_value)
        {
            if ($value === $_value)
            {
                ++$count;
            }
        }
        return $count;
    }

    // //////////////////////////   Strings   ////////////////////////////

    /**
     * Checks if is a valid url.
     *
     * @see https://gist.github.com/dperini/729294
     *
     * @param bool $webonly Put local urls as invalid ( eg : "http://localhost/index.php" )
     */
    public static function isValidUrl(string $url, bool $webonly = false): bool
    {
        return preg_match($webonly ? self::WEB_URL_REGEX : self::LOCAL_URL_REGEX, $url) > 0;
    }

    /**
     * Convert CamelCased to camel_cased.
     */
    public static function to_snake(string $camelCased): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($camelCased)));
    }

    /**
     * Convert snake_case to snakeCase.
     */
    public static function toCamelCase(string $snake_case): string
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match)
        {
            return ('.' === $match[1] ? '_' : '') . strtoupper($match[2]);
        }, $snake_case);
    }

    /**
     * Return current Unix timestamp in milliseconds.
     *
     * @see https://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php
     */
    public static function millitime(): int
    {
        list($usec, $sec) = explode(' ', microtime());
        return (int) ((int) $sec * 1000 + ((float) $usec * 1000));
    }

    /**
     * Generates a uuid V4.
     *
     * @see https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     */
    public static function generate_uuid_v4(): string
    {
        if (true === function_exists('com_create_guid'))
        {
            return trim(\com_create_guid(), '{}');
        }
        $data    = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Returns whether this string consists entirely of ASCII characters.
     */
    public static function isAscii(string $input): bool
    {
        return 0 === \preg_match('/[^\x00-\x7F]/', $input);
    }

    /**
     * Returns whether this string consists entirely of printable ASCII characters.
     */
    public static function isPrintableAscii(string $input): bool
    {
        return 0 === \preg_match('/[^\x20-\x7E]/', $input);
    }

    /**
     * Get Human Readable file size.
     *
     * @see https://gist.github.com/liunian/9338301
     *
     * @staticvar array $units
     */
    public static function getFilesize(float|int $size, int $precision = 2): string
    {
        static $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step         = 1024;
        $i            = 0;

        while (($size / $step) >= 1)
        {
            $size /= $step;
            ++$i;
        }
        return round($size, $precision) . $units[$i];
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     */
    public static function randomString(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length)
        {
            $size  = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Get the size of the longest word on a string.
     */
    public static function getWordSize(string|\Stringable $string): int
    {
        $string = (string) $string;
        $len    = 0;

        foreach (preg_split('#[\h\v]+#', $string) as $word)
        {
            if (($wlen = \mb_strlen($word)) > $len)
            {
                $len = $wlen;
            }
        }

        return $len;
    }

    /**
     * Split the string at the given length without cutting words.
     *
     * @param int &$length
     */
    public static function splitString(string|\Stringable $string, &$length = null): array
    {
        $string    = preg_replace('#[\v\h]+#', ' ', (string) $string);

        if ( ! is_int($length))
        {
            $length = 0;
        }

        $length    = max(0, $length);

        $strlen    = \mb_strlen($string);

        if (0 === $length || $strlen < $length)
        {
            $length = ! $length ? $strlen : $length;
            return [$string];
        }
        $result    = [];
        $words     = preg_split('#[\h\v]+#', $string);

        // get the longer word length
        $maxLength = max($length, self::getWordSize($string));

        if ($maxLength > $length)
        {
            $length = $maxLength;
        }

        $line      = '';

        foreach ($words as $index => $word)
        {
            $lineLength = \mb_strlen($line);

            if (0 === $lineLength)
            {
                $line = $word;
            } elseif ($lineLength + \mb_strlen($word) + 1 > $length)
            {
                $result[] = $line;
                $line     = $word;
            } else
            {
                $line .= " {$word}";
            }

            if ( ! isset($words[$index + 1]))
            {
                $result[] = $line;
            }
        }

        return $result;
    }

    /**
     * Joins iterable together using provided glue.
     */
    public static function join(mixed $glue, iterable $values): string
    {
        $glue   = str_val($glue);

        $result = [];

        foreach ($values as $value)
        {
            $result[] = str_val($value);
        }

        return implode($glue, $result);
    }

    /**
     * Try to reproduce python format.
     */
    public static function format(string $message, mixed ...$args): string
    {
        return $message;
    }

    /**
     * Split a stringable using provided separator.
     */
    public static function split(mixed $separator, mixed $value, int $limit = -1): array
    {
        $separator = str_val($separator);

        $value     = str_val($value);

        if (0 === $limit)
        {
            return [$value];
        }

        if ($limit > 0 && $limit < PHP_INT_MAX)
        {
            ++$limit;
        }

        $method    = 'preg_split';

        if ( ! preg_valid($separator))
        {
            $method = 'explode';

            if ($limit < 0)
            {
                $limit = PHP_INT_MAX;
            }
        }

        return $method($separator, $value, $limit);
    }

    // //////////////////////////   Time   ////////////////////////////

    /**
     * Get script execution time.
     */
    public static function getExecutionTime(int $precision = 6): float|int
    {
        return round(microtime(true) - \SCRIPT_START, $precision);
    }

    /**
     * Pauses script execution for a given amount of time
     * combines sleep or usleep.
     */
    public static function pause(float|int $seconds): void
    {
        /*
         * @link https://www.php.net/manual/en/function.usleep.php
         * Note: Values larger than 1000000 (i.e. sleeping for more than a second) may not be supported by the operating system.
         * Use sleep() instead.
         */
        if ($seconds <= 0)
        {
            return;
        }

        $iseconds     = intval(floor($seconds));
        $seconds -= $iseconds;
        $microseconds = (int) round($seconds / self::MICROSECOND);

        if ($iseconds > 0)
        {
            sleep($iseconds);
        }

        if ($microseconds > 0)
        {
            usleep($microseconds);
        }
    }

    /**
     * Pauses script execution for a given amount of milliseconds.
     */
    public static function msleep(int $milliseconds): void
    {
        self::pause($milliseconds * self::MILLISECOND);
    }

    // //////////////////////////   Classes   ////////////////////////////

    /**
     * Get class implementing given parent class from the loaded classes.
     *
     * @throws \InvalidArgumentException
     */
    public static function implements_class(object|string $parentClass, bool $instantiable = true): array
    {
        static $parsed       = [
            'count'   => 0,
            'classes' => [],
        ];

        $cache               = &$parsed['classes'];
        $count               = &$parsed['count'];

        $instantiable        = (int) $instantiable;

        if (is_object($parentClass))
        {
            $parentClass = get_class($parentClass);
        }

        if ( ! class_exists($parentClass) && ! interface_exists($parentClass))
        {
            throw new \InvalidArgumentException(sprintf('Invalid class %s', $parentClass));
        }

        $iterator            = array_reverse(get_declared_classes());

        if (count($iterator) !== $count)
        {
            $cache = [];
            $count = count($iterator);
        }

        if (isset($cache[$parentClass]))
        {
            return $cache[$parentClass][$instantiable];
        }

        $cache[$parentClass] = [[], []];

        $result              = &$cache[$parentClass];

        foreach ($iterator as $class)
        {
            if (is_a($class, $parentClass, true))
            {
                $result[0][$class] = $class;

                if ((new \ReflectionClass($class))->isInstantiable())
                {
                    $result[1][$class] = $class;
                }
            }
        }

        return $result[$instantiable];
    }

    /**
     * Get Constants defined in a class.
     *
     * @phan-suppress PhanParamTooManyInternal
     *
     * @param bool $public if True returns only public visibility constants
     */
    public static function getClassConstants(object|string $class, bool $public = true): array
    {
        static $parsed = [
            [], [],
        ];

        if (is_object($class))
        {
            $class = get_class($class);
        }

        if ( ! class_exists($class) && ! interface_exists($class))
        {
            return [];
        }

        $cache         = &$parsed[(int) $public];

        if ( ! isset($cache[$class]))
        {
            try
            {
                $result        = [];
                $filter        = $public ? \ReflectionClassConstant::IS_PUBLIC : null;

                foreach (array_reverse(class_parents($class) ?: []) + [$class => $class] as $className)
                {
                    $reflector = new \ReflectionClass($className);

                    foreach ($reflector->getConstants($filter) as $constant => $value)
                    {
                        if (array_key_exists($constant, $result))
                        {
                            continue;
                        }
                        $result[$constant] = $value;
                    }
                }

                $cache[$class] = $result;
            } catch (\ReflectionException)
            {
                return $cache[$class] = [];
            }
        }
        return $cache[$class];
    }

    /**
     * Call a method within an object ignoring its status.
     */
    public static function callPrivateMethod(object $instance, string $method, mixed ...$arguments): mixed
    {
        /**
         * Caches context.
         */
        static $contexts = [], $baseClosure;

        $baseClosure ??= function (string $method, mixed ...$arguments)
        {
            return $this->{$method}(...$arguments);
        };

        $class           = get_class($instance);

        if ( ! isset($contexts[$class][$method]))
        {
            if (self::isPublicMethod($instance, $method))
            {
                return $instance->{$method}(...$arguments);
            }

            // context for protected method
            $context                   = $class;

            try
            {
                $reflector = new \ReflectionMethod($instance, $method);

                if ($reflector->isStatic())
                {
                    throw new \ReflectionException();
                }

                // context for private method
                if ($reflector->isPrivate())
                {
                    $context = $reflector->getDeclaringClass()->getName();
                }
            } catch (\ReflectionException)
            {
                throw new \BadMethodCallException(sprintf('Call to undefined non static method %s::%s()', get_class($instance), $method));
            }

            $contexts[$class][$method] = $context;
        }

        $closure         = $baseClosure->bindTo($instance, $contexts[$class][$method]);

        return $closure($method, ...$arguments);
    }

    private static function isPublicMethod(object $instance, string $method): bool
    {
        static $cache = [];

        $class        = get_class($instance);

        if ( ! isset($cache[$class]))
        {
            $cache[$class] = [];
        }

        if ( ! is_bool($cache[$class][$method] ?? null))
        {
            try
            {
                $reflector              = new \ReflectionMethod($instance, $method);
                $cache[$class][$method] = $reflector->isPublic() && ! $reflector->isStatic();
            } catch (\ReflectionException)
            {
                $cache[$class][$method] = false;
            }
        }

        return $cache[$class][$method];
    }
}
