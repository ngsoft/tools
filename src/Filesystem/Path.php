<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

/**
 * Node Js node:path like
 */
final class Path
{

    public static function basename(string $path, string $suffix = ''): string
    {
        $path = normalize_path($path);
        $basename = self::basename($path);

        if (str_ends_with($path, $suffix))
        {

        }
    }

    private function __construct()
    {

    }

}
