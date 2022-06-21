<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use NGSOFT\Tools;

if (defined(__NAMESPACE__ . NAMESPACE_SEPARATOR . 'MICROSECOND')) {
    return;
}

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

/**
 * Execute callable forcing the error handler to suppress errors
 * Exceptions thrown works as intended
 *
 * @param callable $callable
 * @param mixed $arguments
 * @return mixed
 */
function safe(callable $callable, mixed ...$arguments): mixed
{
    return Tools::safe_exec($callable, ...$arguments);
}