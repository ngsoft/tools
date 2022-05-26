<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Exceptions\NotFoundException;
use Psr\Container\{
    ContainerInterface, NotFoundExceptionInterface
};
use ReflectionException,
    ReflectionFunction,
    ReflectionFunctionAbstract,
    ReflectionMethod,
    ReflectionNamedType,
    ReflectionParameter,
    ReflectionUnionType,
    RuntimeException,
    Throwable;

/**
 * A Simple Resolver that autowire classes and functions
 */
class Resolver {

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Resolves a className
     *
     * @param string $className
     * @return ?object
     */
    public function resolveClassName(string $className): ?object {
        $result = null;
        if (class_exists($className)) {
            if (!method_exists($className, '__construct')) return new $className();
            $reflection = new ReflectionMethod($className, '__construct');
            try {
                $args = $this->resolveParameters($reflection);
                $result = new $className(...$args);
            } catch (RuntimeException $error) {
                if ($error instanceof NotFoundExceptionInterface) throw $error;
            }
        }
        return $result;
    }

    /**
     * Resolves a callable
     * @param callable $callable
     * @return mixed
     * @throws RuntimeException
     */
    public function resolveCallable(callable $callable): mixed {
        $result = null;
        $closure = $callable instanceof Closure ? $callable : Closure::fromCallable($callable);
        $reflection = new ReflectionFunction($closure);

        try {
            $args = $this->resolveParameters($reflection);
            $result = call_user_func_array($callable, $args);
        } catch (RuntimeException $error) {
            if ($error instanceof NotFoundException) throw $error;
        }
        return $result;
    }

    /**
     * @param ReflectionUnionType $type
     * @param ReflectionParameter $param
     * @return mixed
     * @throws RuntimeException
     */
    protected function resolveUnionType(ReflectionUnionType $type, ReflectionParameter $param): mixed {
        /** @var ReflectionNamedType $namedType */
        foreach ($type->getTypes() as $namedType) {

            try {

                if ($resolved = $this->resolveNamedType($namedType, $param)) {
                    return $resolved;
                }
            } catch (Throwable) {

            }
        }

        if ($type->allowsNull()) return null;
        throw new RuntimeException();
    }

    /**
     * @param ReflectionNamedType $type
     * @param ReflectionParameter $param
     * @return mixed
     * @throws RuntimeException
     * @throws NotFoundException
     */
    protected function resolveNamedType(ReflectionNamedType $type, ReflectionParameter $param): mixed {

        if ($type->isBuiltin()) {
            $allowsNull = $type->allowsNull();

            try {
                if ($param->isDefaultValueAvailable()) {
                    // ReflectionException can be thrown here
                    $resolved = $param->getDefaultValue();
                    return $resolved;
                } elseif ($allowsNull) {
                    return null;
                }
            } catch (ReflectionException) {

            }
            throw new RuntimeException();
        }


        /** @var ReflectionNamedType $type */
        $className = $type->getName();

        if (in_array($className, ['self', 'static'])) {
            $className = $param->getDeclaringClass()->getName();
        }


        try {
            $resolved = $this->container->get($className);
            return $resolved;
        } catch (NotFoundException $error) {
            if (!$type->allowsNull()) throw $error;
            return null;
        }
    }

    /**
     * @param ReflectionParameter $param
     * @return mixed
     * @throws RuntimeException
     */
    protected function resolveType(ReflectionParameter $param): mixed {
        $type = $param->getType();

        if ($type === null) {
            try {
                if ($param->isDefaultValueAvailable()) {
                    $resolved = $param->getDefaultValue();
                    return $resolved;
                }
            } catch (ReflectionException) {

            }
            return null;
        } elseif ($type instanceof ReflectionNamedType) {
            return $this->resolveNamedType($type, $param);
        } elseif ($type instanceof ReflectionUnionType) return $this->resolveUnionType($type, $param);
        elseif ($type->allowsNull()) return null;
        // intersection types not supported
        throw new RuntimeException();
    }

    /**
     * Resolves Method Type Hints using Container
     *
     * @param ReflectionFunctionAbstract $reflection
     * @return array
     * @throws RuntimeException
     */
    public function resolveParameters(ReflectionFunctionAbstract $reflection): array {
        $result = [];
        if ($reflection->getNumberOfParameters() == 0) return $result;

        foreach ($reflection->getParameters() as $index => $param) {
            try {
                $result[] = $this->resolveType($param);
            } catch (Throwable) {
                throw new RuntimeException(sprintf('Cannot resolve parameter %u', $index));
            }
        }

        return $result;
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        return [];
    }

}
