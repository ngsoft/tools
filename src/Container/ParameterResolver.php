<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use NGSOFT\Profiler\Profiler;
use function is_instanciable;

class ParameterResolver
{

    protected Profiler $profiler;

    public function __construct(
    )
    {
        $this->profiler = new Profiler();
    }

    public function canResolve(string $id, mixed $value): bool
    {
        return is_instanciable($id) || $value !== null;
    }

    public function resolveCallable(string|array|\Closure $callable, array $providedParameters = []): mixed
    {

    }

}
