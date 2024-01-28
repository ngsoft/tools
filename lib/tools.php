<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use NGSOFT\Tools;

if (defined(__NAMESPACE__ . NAMESPACE_SEPARATOR . 'MICROSECOND'))
{
    return;
}

const MICROSECOND = 1e-6;
const MILLISECOND = 1e-3;
const SECOND      = 1;
const MINUTE      = 60;
const HOUR        = 3600;
const DAY         = 86400;
const WEEK        = 604800;
const MONTH       = 2628000;
const YEAR        = 31536000;

/**
 * Iterate iterable.
 */
function iterate_all(iterable $iterable): array
{
    return Tools::iterateAll($iterable);
}

/**
 * Count number of occurrences of value.
 */
function count_value(mixed $value, iterable $iterable): int
{
    return Tools::countValue($value, $iterable);
}

/**
 * Tests if at least one element in the iterable passes the test implemented by the provided function.
 */
function some(callable $callback, iterable $iterable): bool
{
    return Tools::some($callback, $iterable);
}

/**
 * Tests if all elements in the iterable pass the test implemented by the provided function.
 */
function every(callable $callback, iterable $iterable): bool
{
    return Tools::every($callback, $iterable);
}

/**
 * Same as the original except callback accepts more arguments and works with string keys.
 *
 * @param callable $callback accepts $value, $key, $array
 */
function map(callable $callback, iterable $iterable): array
{
    return Tools::map($callback, $iterable);
}

/**
 * Filters elements of an iterable using a callback function.
 *
 * @param callable $callback accepts $value, $key, $array
 */
function filter(callable $callback, iterable $iterable): array
{
    return Tools::filter($callback, $iterable);
}

/**
 * Searches an iterable until element is found.
 *
 * @return null|mixed
 */
function search_iterable(callable $callback, iterable $iterable): mixed
{
    return Tools::search($callback, $iterable);
}

/**
 * Get the size of the longest word on a string.
 */
function str_word_size(string|\Stringable $string): int
{
    return Tools::getWordSize($string);
}

/**
 * Split the string at the given length without cutting words.
 *
 * @param int &$length
 */
function split_string(string|\Stringable $string, &$length = null): array
{
    return Tools::splitString($string, $length);
}

/**
 * Uses callback for each element of the array and returns the value.
 */
function each(callable $callback, iterable $iterable): void
{
    Tools::each($callback, $iterable);
}

/**
 * Get a value(s) from the array, and remove it.
 */
function pull(int|iterable|string $keys, array|\ArrayAccess &$iterable): mixed
{
    return Tools::pull($keys, $iterable);
}

/**
 * Converts an iterable to an array recursively
 * if the keys are not string they will be indexed.
 */
function iterable_to_array(iterable $iterable): array
{
    return Tools::iterableToArray($iterable);
}

/**
 * Concatenate multiple values into the iterable provided recursively
 * If a provided value is iterable it will be merged into the iterable
 * (non-numeric keys will be replaced if not iterable into the provided object).
 */
function concat(array|\ArrayAccess &$iterable, mixed ...$values): array|\ArrayAccess
{
    return Tools::concat($iterable, ...$values);
}

/**
 * Pauses script execution for a given amount of time
 * uses sleep or/and usleep.
 */
function pause(float|int $seconds): void
{
    Tools::pause($seconds);
}

/**
 * Pauses script execution for a given amount of milliseconds.
 */
function msleep(int $milliseconds): void
{
    Tools::msleep($milliseconds);
}

/**
 * Execute callable forcing the error handler to suppress errors
 * Exceptions thrown works as intended.
 */
function safe(callable $callable, mixed ...$arguments): mixed
{
    return Tools::safe_exec($callable, ...$arguments);
}

/**
 * Joins iterable together using provided glue.
 */
function join(mixed $glue, iterable $values): string
{
    return Tools::join($glue, $values);
}

/**
 * Split a stringable using provided separator.
 */
function split(mixed $separator, mixed $value, int $limit = -1): array
{
    return Tools::split($separator, $value, $limit);
}
