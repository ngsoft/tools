<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use ErrorException;
use NGSOFT\Tools;
use ValueError;
use function get_debug_type;

if (function_exists(__NAMESPACE__ . NAMESPACE_SEPARATOR . 'list_files')) {
    return;
}

/**
 * List all files inside a directory
 *
 * @param string|Directory $directory
 * @param string|array $extensions
 * @param bool $hidden
 * @param bool $recursive
 * @return string[]
 */
function list_files(string|Directory $directory, string|array $extensions = [], bool $hidden = false, bool $recursive = false): array
{
    if (is_string($directory)) {
        $directory = Directory::create($directory);
    }
    $iterator = $recursive ? $directory->allFiles($extensions, $hidden) : $directory->files($extensions, $hidden);
    return $iterator->files();
}

/**
 * List all files inside a directory recursively
 *
 * @param string|Directory $directory
 * @param string|array $extensions
 * @param bool $hidden
 * @return string[]
 */
function list_files_recursive(string|Directory $directory, string|array $extensions = [], bool $hidden = false): array
{
    return list_files($directory, $extensions, $hidden, true);
}

/**
 * List directories inside a directory
 *
 * @param string|Directory $directory
 * @param bool $recursive
 * @return string[]
 */
function list_directories(string|Directory $directory, bool $recursive = false): array
{
    if (is_string($directory)) {
        $directory = Directory::create($directory);
    }
    return $directory->directories($recursive)->files();
}

/**
 * Require file in context isolation
 * use it in a try/catch block
 *
 * @param string $file
 * @param array $data
 * @param bool $once
 * @return mixed
 */
function require_file(string $file, array $data = [], bool $once = false): mixed
{

    if ( ! is_file($file)) {
        return null;
    }

    $closure = static function (array $___data): mixed {
        extract($___data);
        unset($___data);
        return func_get_arg(2) ?
        require_once func_get_arg(1) :
        require func_get_arg(1);
    };

    try {
        // Warnings will be thrown as ErrorException
        set_error_handler(function ($type, $msg, $file, $line) {
            if ( ! (error_reporting() & $type)) { return false; }
            throw new ErrorException($msg, 0, $type, $file, $line);
        });

        return $closure($data, $file, $once);
    } finally { restore_error_handler(); }
}

/**
 * Require file once in context isolation
 *
 * @param string $file
 * @param array $data
 * @return mixed
 */
function require_file_once(string $file, array $data = []): mixed
{
    return require_file($file, $data, true);
}

/**
 * Require multiple files at once
 *
 * @param string|iterable $files can be an array of files or directories
 * @param array $data data to extract to the files
 * @param bool $once use require_once
 * @return iterable iterator of file => result
 * @throws ValueError
 */
function require_all(string|iterable $files, array $data = [], bool $once = false): iterable
{
    if ( ! is_iterable($files)) {
        $files = [$files];
    }
    $result = [];
    foreach ($files as $file) {

        if ( ! is_string($file)) {
            throw new ValueError(sprintf('Invalid type %s for requested type string.', get_debug_type($file)));
        }

        if (array_key_exists($file, $result)) {
            continue;
        }

        if ( ! file_exists($file)) {
            $result[$file] = null;
            continue;
        }

        if (is_file($file)) {
            $result [$file] = require_file($file, $data, $once);
            continue;
        }

        foreach (list_files_recursive($file, 'php') as $file) {
            $result[$file] = require_file($file, $data, $once);
        }
    }

    return $result;
}

/**
 * Require multiple files at once but only once
 *
 * @param string|iterable $files
 * @param array $data
 * @return iterable
 */
function require_all_once(string|iterable $files, array $data = []): iterable
{
    return require_all($files, $data, true);
}

/**
 * Normalize pathnames
 *
 * @param string $path
 * @return string
 */
function normalize_path(string $path): string
{
    return Tools::normalize_path($path);
}
