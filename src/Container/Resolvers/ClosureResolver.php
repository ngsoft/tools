<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use Closure;
use NGSOFT\Container\{
    ContainerInterface, ContainerResolver
};

/** @phan-file-suppress PhanUnusedPublicMethodParameter */
class ClosureResolver implements ContainerResolver
{

    public function getDefaultPriority(): int
    {
        return ContainerInterface::PRIORITY_HIGH + 2;
    }

    public function __invoke(ContainerInterface $container, string $id, mixed $value): mixed
    {
        return value($value, $container);
    }

}
