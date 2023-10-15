<?php

declare(strict_types=1);

// Set the internal encoding
mb_internal_encoding('UTF-8');

$scripts = [
    'errors',
    'support',
    'helpers',
    'tools',
    'filesystem',
    'types',
];

foreach ($scripts as $name)
{
    require_once __DIR__ . DIRECTORY_SEPARATOR . $name . '.php';
}
