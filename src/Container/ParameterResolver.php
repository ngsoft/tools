<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Container\Exceptions\ResolverException,
    Psr\Container\ContainerExceptionInterface,
    ReflectionClass,
    ReflectionException,
    ReflectionFunction,
    ReflectionIntersectionType,
    ReflectionMethod,
    ReflectionParameter;
use function is_instanciable,
             NGSOFT\Tools\map;

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

    public function resolve(string|array|object $callable, array $providedParameters = []): mixed
    {
        static $builtin = [
            'self', 'parent', 'static',
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'void', 'never', 'null', 'false',
        ];
        $success = false;

        $class = $method = null;
        $className = null;
        $isClosure = false;

        try {

            if (is_string($callable)) {
                $reflector = new ReflectionClass($className = $class = $callable);
                if ( ! $reflector->isInstantiable()) {
                    throw new ReflectionException();
                }
                if ( ! ($reflector = $reflector->getConstructor())) {
                    return new $class();
                }
            } elseif ($isClosure = $callable instanceof Closure) {
                $reflector = new \ReflectionFunction($callable);
            } elseif (is_object($callable)) {
                $class = $callable;
                $callable = [$callable, '__invoke'];
            }

            if (is_array($callable) && count($callable) === 2) {
                [$class, $method] = $callable;
                $className = is_object($class) ? get_class($class) : $class;
                $reflector = new ReflectionMethod($class, $method);
            }

            if ( ! isset($reflector)) {
                throw new ReflectionException();
            }
        } catch (\ReflectionException) {
            throw ResolverException::invalidCallable($callable);
        }

        /** @var ReflectionMethod|ReflectionFunction $reflector */
        /** @var ReflectionParameter[] $reflParams */
        $reflParams = $reflector->getParameters();

        $names = map(fn($p) => $p->getName(), $reflParams);

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

            $nullable = $reflParam->allowsNull();

            if ($reflParam->isVariadic()) {
                $params[$name] = [];

                if (array_key_exists($name, $provided)) {

                    foreach ((array) $provided[$name] as $value) {
                        $params[$name][] = $value;
                    }
                    continue;
                }

                while ($value = array_shift($provided)) {
                    $params[$name][] = $value;
                }
                continue;
            }

            if (array_key_exists($name, $provided)) {
                $params[$name] = $provided[$name];
                unset($provided[$name]);
                continue;
            }

            if (array_key_exists($index, $provided)) {
                $params[$name] = $provided[$index];
                unset($provided[$index]);
                continue;
            }

            if ( ! $reflParam->canBePassedByValue() && $reflParam->isDefaultValueAvailable()) {
                continue;
            }



            $types[$name] = [];

            if ($type = $reflParam->getType()) {
                if ($type instanceof ReflectionIntersectionType) {
                    throw new ResolverException(
                                    sprintf('Cannot resolve intersection type param #%d: $%s',
                                            $index,
                                            $name
                                    )
                    );
                }
                foreach (explode('|', (string) $type) as $dep) {

                    if (str_starts_with($dep, '?')) {
                        $dep = substr($dep, 1);
                        $nullable = true;
                    }

                    if ($dep === 'self' && $class) {
                        $dep = is_string($class) ? $class : get_class($class);
                    } elseif (in_array($dep, $builtin)) {
                        continue;
                    }

                    try {
                        $params[$name] = $this->container->get($dep);
                        continue 2;
                    } catch (ContainerExceptionInterface) {

                    }
                }
            }



            if ($reflParam->isDefaultValueAvailable()) {
                continue;
            }

            if ($nullable) {
                $params[$name] = null;
                continue;
            }

            throw new ResolverException(
                            sprintf(
                                    'Cannot resolve %s%s() parameter #%d %s $%s',
                                    $className ? "$className::" : '',
                                    $method ? $method : ($class ? '__construct' : Closure),
                                    $index, $type ?? 'mixed', $name
                            )
            );
        }


        if ($isClosure) {
            return $reflector->invokeArgs($params);
        }

        if ( ! isset($method)) {
            return new $class(...$params);
        }

        if ( ! is_object($class)) {
            $class = $this->container->get($class);
        }

        return $reflector->invokeArgs($class, $params);
    }

}
