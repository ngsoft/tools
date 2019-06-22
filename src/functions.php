<?php

namespace NGSOFT\Tools;

/**
 * Recursively remove directory
 * @param string $src
 * @return bool
 */
function rrmdir(string $src): bool {
    $dir = opendir($src);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if (is_dir($full)) {
                rrmdir($full);
            } else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    return rmdir($src);
}

/**
 * Tests if all elements in the array pass the test implemented by the provided function.
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_every(callable $callback, array $array): bool {
    $newarr = array_filter($array, $callback);
    return empty(array_diff_key($array, $newarr));
}

/**
 * Tests if at least one element in the array passes the test implemented by the provided function..
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_some(callable $callback, array $array): bool {
    return !array_every($callback, $array);
}

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
