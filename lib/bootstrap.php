<?php

declare(strict_types=1);

use NGSOFT\Facades\Facade;

// Set the internal encoding
mb_internal_encoding("UTF-8");

$scripts = [
    'errors',
    'support',
    'helpers',
    'tools',
    'filesystem',
    'symfony',
];

foreach ($scripts as $name) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . $name . '.php';
}



// instanciating the container
Facade::boot();
