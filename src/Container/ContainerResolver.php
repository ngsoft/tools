<?php

declare(strict_types=1);

namespace NGSOFT\Container;

interface ContainerResolver
{

    /**
     * Resolves an entry from the container
     *
     * @param ContainerInterface $container
     * @param string $id entry
     * @param mixed $value current value
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, string $id, mixed $value): mixed;
}
