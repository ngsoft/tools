<?php

declare(strict_types=1);

// Set the internal encoding
mb_internal_encoding("UTF-8");

if (!defined('NAMESPACE_SEPARATOR')) {



    define('NAMESPACE_SEPARATOR', '\\');
}


if (!function_exists('class_basename')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'support.php';
}





if (!defined('NGSOFT\\SCRIPT_START')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'helpers.php';
}

