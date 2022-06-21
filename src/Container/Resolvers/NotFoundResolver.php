<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use NGSOFT\Container\{
    ContainerInterface, ContainerResolver, NotFoundException
};

class NotFoundResolver implements ContainerResolver
{

    public function getDefaultPriority(): int
    {
        return 1;
    }

    public function __invoke(ContainerInterface $container, string $id, mixed $value): mixed
    {

        if ($value === null) {
            throw new NotFoundException($container, $id);
        }
        return $value;
    }

}
