<?php

declare(strict_types=1);

use NGSOFT\{
    Container\Container, Facades\Facade
};

// Set the internal encoding
mb_internal_encoding("UTF-8");

require_once __DIR__ . DIRECTORY_SEPARATOR . 'support.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

// instanciating the container
Facade::setContainer(new NGSOFT\Container\SimpleContainer());
