<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

interface ParameterResolver
{

    public function getParameters(\ReflectionFunction|\ReflectionMethod $reflector, array $provided, array $resolved): array;
}
