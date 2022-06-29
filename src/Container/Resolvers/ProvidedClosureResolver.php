<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use Closure;
use NGSOFT\Container\{
    ContainerInterface, ContainerResolver
};

class ProvidedClosureResolver implements ContainerResolver
{

    public function __construct(
            protected Closure $closure,
            protected int $priority = self::PRIORITY_MEDIUM
    )
    {

    }

    public function getDefaultPriority(): int
    {
        return $this->priority;
    }

    public function resolve(ContainerInterface $container, string $id, mixed $value): mixed
    {
        $closure = $this->closure;
        return $closure($container, $id, $value);
    }

}
