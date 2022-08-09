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

    /**
     *
     * @phan-suppress PhanTypeMismatchArgumentInternalReal,PhanParamTooFewInternal
     */
    public function resolve(string|array|Closure $callable, array $providedParameters = [], bool &$success = null): mixed
    {

        static $builtin = [
            'self', 'parent', 'static',
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'void', 'never', 'null', 'false',
        ];

        $success = false;

        try {

            $class = $method = null;
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
            } else {
                throw new ResolverException('Invalid callable ' . var_export($callable, true));
            }

            /** @var ReflectionMethod|ReflectionFunction $reflector */
            $names = $types = $defaults = $params = $nullable = [];
            $variadic = null;
            $index = 0;
            /** @var ReflectionParameter $reflectParam */
            foreach ($reflector->getParameters() as $reflectParam) {
                $names[$index] = $name = $reflectParam->getName();

                $types[$name] = [];

                if ($type = $reflectParam->getType()) {
                    if ($type instanceof ReflectionIntersectionType && ! isset($providedParameters[$name]) && ! isset($providedParameters[$index])) {
                        throw new ResolverException('Cannot resolve intersection type param: ' . $name);
                    }
                    $types[$name] = preg_split('#[\|]+#', (string) $reflectParam->getType());
                }

                if ($reflectParam->isDefaultValueAvailable()) {
                    $defaults[$name] = $reflectParam->getDefaultValue();
                } elseif ($reflectParam->allowsNull()) {
                    $nullable[$name] = true;
                    $defaults[$name] = null;
                }

                if ($reflectParam->isVariadic()) {
                    $variadic = $name;
                }
                $index ++;
            }



            foreach (array_keys($providedParameters) as $name) {
                if (is_string($name) && ! in_array($name, $names)) {
                    throw new ResolverException('Invalid parameter name: ' . $name);
                }
            }

            $provided = $providedParameters;

            foreach ($names as $index => $name) {

                if ($name === $variadic) {

                    $variadicValue = [];

                    if (isset($provided[$name])) {
                        if ( ! is_array($provided[$name])) {
                            $variadicValue[] = $provided[$name];
                        } else { $variadicValue = $provided[$name]; }
                        unset($provided[$name]);
                    } else {
                        while ($value = array_shift($provided)) {
                            $variadicValue[] = $value;
                        }
                    }

                    foreach ($variadicValue as $value) {
                        $params[] = $value;
                    }

                    continue;
                }

                if (array_key_exists($name, $provided)) {
                    $params[$index] = $provided[$name];
                    unset($provided[$name]);
                    continue;
                } elseif (array_key_exists($index, $provided)) {
                    $params[$index] = $provided[$index];
                    unset($provided[$index]);
                    continue;
                }

                // here we try to get value from container

                foreach ($types[$name] as $type) {

                    if ($type[0] === '?') {
                        $nullable[$name] = true;
                        $type = substr($type, 1);
                    }

                    if ($type === 'self' && $class) {
                        $type = is_string($class) ? $class : get_class($class);
                    } elseif (in_array($type, $builtin)) {
                        continue;
                    }



                    try {
                        $value = $this->container->get($type);
                        $params[] = $value;
                        continue 2;
                    } catch (ContainerExceptionInterface) {

                    }
                }


                // definition using container without type
                if ($index === 0 && empty($types[$name]) && $isClosure && count($names) === 1) {
                    $params[] = $this->container;
                    continue;
                }

                if (array_key_exists($name, $defaults)) {
                    $params[$index] = $defaults[$name];
                    continue;
                }

                if (isset($nullable[$name])) {
                    $params[$index] = null;
                    continue;
                }

                throw new ResolverException('Cannot resolve parameter #' . $index . ': $' . $name);
            }




            if (isset($class)) {

                /** @var ReflectionMethod $reflector */
                if ( ! isset($method)) {
                    $success = true;
                    return (new ReflectionClass($class))->newInstanceArgs($params);
                } else {

                    if ($reflector->isStatic()) {
                        if (is_object($class)) {
                            $class = get_class($class);
                        }
                        $success = true;
                        return $class::{$method}(...$params);
                    }

                    if ( ! is_object($class)) {
                        $class = $this->container->get($class);
                    }
                    $success = true;
                    return $reflector->invokeArgs($class, $params);
                }
            } else {
                /** @var ReflectionFunction $reflector */
                $success = true;

                return $reflector->invokeArgs($params);
            }
        } catch (ReflectionException $prev) {
            throw new ResolverException('Cannot resolve ' . (is_string($callable) ? $callable : var_export($callable, true)), previous: $prev);
        }
    }

}
