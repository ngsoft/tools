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
 * Tests whether all elements in the array pass the test implemented by the provided function.
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_every(callable $callback, array $array): bool {
    $newarr = array_filter($array, $callback);
    return empty(array_diff_key($array, $newarr));
}

/**
 * Tests whether at least one element in the array passes the test implemented by the provided function..
 * @param callable $callback A function to test for each element
 * @param array $array
 * @return bool
 */
function array_some(callable $callback, array $array): bool {
    return !array_every($callback, $array);
}
