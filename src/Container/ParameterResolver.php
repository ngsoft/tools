<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    ReflectionClass,
    ReflectionException,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter;
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
        try {
            $reflector = $class = $method = null;
            $isClosure = $callable instanceof \Closure;
            if (is_string($callable)) {
                $class = $callable;
                $reflector = new ReflectionClass($class);
                if ( ! $reflector->isInstantiable()) {
                    return null;
                }
                if ( ! ($reflector = $reflector->getConstructor())) {
                    return new $class();
                }
            } elseif ($isClosure) {
                $reflector = new ReflectionFunction($callable);
            } elseif (count($callable) === 2) {
                [$class, $method] = $callable;
                $reflector = new ReflectionMethod($class, $method);
            }

            /** @var ReflectionMethod|ReflectionFunction $reflector */
            $names = $types = [];
            /** @var ReflectionParameter $reflectParam */
            foreach ($reflector->getParameters() as $reflectParam) {
                $names[] = $name = $reflectParam->getName();
                $types[$name] = (string) $reflectParam->getType();
            }

            var_dump($names, $types);
        } catch (ReflectionException) {
            return null;
        }
    }

}
