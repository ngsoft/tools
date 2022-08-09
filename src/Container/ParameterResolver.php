<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\Container\{
    Attribute\Inject, Exceptions\ResolverException
};
use Psr\Container\ContainerExceptionInterface,
    ReflectionAttribute,
    ReflectionClass,
    ReflectionException,
    ReflectionFunction,
    ReflectionIntersectionType,
    ReflectionMethod,
    ReflectionParameter,
    Throwable;
use function is_instanciable,
             str_starts_with;

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

    protected function parseAttributes(ReflectionMethod|ReflectionParameter $reflector, array $providedParameters): array
    {

        try {
            /** @var ReflectionAttribute $attribute */
            /** @var Inject $inject */
            foreach ($reflector->getAttributes(Inject::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $inject = $attribute->newInstance();
                if ($reflector instanceof ReflectionParameter) {
                    if ( ! empty($inject->name)) {
                        $providedParameters[$reflector->getName()] ??= $this->container->get($inject->name);
                    }

                    continue;
                }

                foreach ($inject->parameters as $index => $id) {
                    $providedParameters[$index] ??= $this->container->get($id);
                }
            }
        } catch (Throwable $prev) {
            throw new ResolverException('Invalid attribute #[Inject]', previous: $prev);
        }

        return $providedParameters;
    }

    public function resolve(string|array|object $callable, array $providedParameters = []): mixed
    {
        static $builtin = [
            'self', 'parent', 'static',
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'void', 'never', 'null', 'false',
        ];

        $class = $method = null;
        $className = null;

        try {

            if (is_string($callable)) {
                $reflector = new ReflectionClass($className = $class = $callable);
                if ( ! $reflector->isInstantiable()) {
                    throw new ReflectionException();
                }
                if ( ! ($reflector = $reflector->getConstructor())) {
                    return new $class();
                }
            } elseif ($callable instanceof Closure) {
                $reflector = new \ReflectionFunction($callable);
            } elseif (is_object($callable)) {
                $callable = [$callable, '__invoke'];
            }

            if (is_array($callable) && count($callable) === 2) {
                [$class, $method] = $callable;
                $className = is_object($class) ? get_class($class) : $class;
                $reflector = new ReflectionMethod($class, $method);

                $providedParameters = $this->parseAttributes($reflector, $providedParameters);
            }

            if ( ! isset($reflector)) {
                throw new ReflectionException();
            }
        } catch (ReflectionException) {
            throw ResolverException::invalidCallable($callable);
        }

        /** @var ReflectionMethod|ReflectionFunction $reflector */
        /** @var ReflectionParameter[] $reflParams */
        $reflParams = $reflector->getParameters();

        $names = array_map(fn($p) => $p->getName(), $reflParams);

        foreach (array_keys($providedParameters) as $name) {
            if (is_string($name)) {
                if ( ! in_array($name, $names)) {
                    throw new ResolverException('Invalid provided param name: ' . $name);
                }
            }
        }

        $provided = $providedParameters;
        $params = [];

        foreach ($reflParams as $index => $reflParam) {

            $name = $names[$index];

            $provided = $this->parseAttributes($reflParam, $provided);

            $nullable = $reflParam->allowsNull();

            if ($reflParam->isVariadic()) {

                if (empty($provided)) {
                    continue;
                }


                if (array_key_exists($name, $provided)) {

                    foreach ((array) $provided[$name] as $value) {
                        $params[] = $value;
                    }
                    continue;
                }

                while ($value = array_shift($provided)) {
                    $params[] = $value;
                }
                continue;
            }

            if (array_key_exists($name, $provided)) {
                $params[$index] = $provided[$name];
                unset($provided[$name]);
                continue;
            }

            if (array_key_exists($index, $provided)) {
                $params[$index] = $provided[$index];
                unset($provided[$index]);
                continue;
            }

            // Values passed by reference not working excepts if ignoring param when param as default value
            if ( ! $reflParam->canBePassedByValue()) {

                throw new ResolverException(
                                sprintf('Cannot resolve Argument #%d (&$%s) that can only be passed by reference.',
                                        $index, $name
                                )
                );
            }

            if ($type = $reflParam->getType()) {
                if ($type instanceof ReflectionIntersectionType) {
                    throw new ResolverException(
                                    sprintf('Cannot resolve intersection type param #%d: $%s',
                                            $index,
                                            $name
                                    )
                    );
                }

                // a small hack to get union/named type as array
                foreach (explode('|', (string) $type) as $dep) {
                    // ?ClassName
                    if (str_starts_with($dep, '?')) {
                        $dep = substr($dep, 1);
                        $nullable = true;
                    }
                    // careful there on Circular dependency when instanciating
                    if ($dep === 'self') {
                        $dep = $reflParam->getDeclaringClass()->getName();
                    } elseif (in_array($dep, $builtin)) {
                        continue;
                    }

                    try {
                        $params[$index] = $this->container->get($dep);
                        continue 2;
                    } catch (ContainerExceptionInterface) {

                    }
                }
            }



            if ($reflParam->isDefaultValueAvailable()) {

                try {
                    $params[$index] = $reflParam->getDefaultValue();
                    continue;
                } catch (ReflectionException) {

                }
            }


            if ($nullable) {
                $params[$index] = null;
                continue;
            }

            throw new ResolverException(
                            sprintf(
                                    'Cannot resolve %s%s() Argument #%d ($%s) of type %s',
                                    $className ? "$className::" : '',
                                    $method ? $method : ($class ? '__construct' : Closure::class),
                                    $index, $name, $type ?? 'mixed'
                            )
            );
        }


        // Closure(...$params)
        if ($reflector instanceof ReflectionFunction) {
            return $reflector->invokeArgs($params);
        }




        // new Classname(...$params)
        if ( ! isset($method)) {
            return (new ReflectionClass($class))->newInstanceArgs($params);
        }

        // static method
        if ($reflector->isStatic()) {
            return $className::{$method}(...array_values($params));
        }

        // [Classname, Method]
        if ( ! is_object($class)) {
            $class = $this->container->get($className);
        }
        // $instance->method(...$params)
        return $reflector->invokeArgs($class, $params);
    }

}
