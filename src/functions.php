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
