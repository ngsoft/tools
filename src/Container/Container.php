<?php

declare(strict_types=1);

namespace NGSOFT\Container;

interface Container extends \Psr\Container\ContainerInterface
{

    /**
     * Add a definition to the container
     *
     * @param string $id
     * @param mixed $entry
     * @return void
     */
    public function set(string $id, mixed $entry): void;
}
