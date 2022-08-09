<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use function is_instanciable;

class ParameterResolver
{

    public function __construct(
            protected ContainerInterface $container
    )
    {

    }

    public function canResolve(string $id, mixed $value): bool
    {
        return is_instanciable($id) || $value !== null;
    }

    public function resolve(string|array|Closure $callable, array $providedParameters = []): mixed
    {

    }

}
