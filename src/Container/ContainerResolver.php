<?php

declare(strict_types=1);

namespace NGSOFT\Container;

interface ContainerResolver
{

    /**
     * Resolves an entry from the container
     */
    public function resolve(ContainerInterface $container, string $id, mixed $value): mixed;

    /**
     * Set the default priority
     */
    public function getDefaultPriority(): int;
}
