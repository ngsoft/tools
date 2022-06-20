<?php

declare(strict_types=1);

use NGSOFT\{
    Container\Container, Facades\Facade
};

// Set the internal encoding
mb_internal_encoding("UTF-8");

if ( ! function_exists('class_basename')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'support.php';
}

if ( ! function_exists('NGSOFT\\Tools\\some')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'helpers.php';
}

// instanciating the container
Facade::setContainer(new NGSOFT\Container\SimpleContainer());
