<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use NGSOFT\Tools;

/**
 * Normalize pathname.
 */
function normalize_path(string $path): string
{
    return Tools::normalize_path($path);
}
