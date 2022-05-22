<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface,
    ReflectionException,
    ReflectionFunction,
    ReflectionFunctionAbstract,
    ReflectionMethod,
    ReflectionNamedType,
    ReflectionType,
    RuntimeException;

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
                if ($error instanceof NotFoundException) throw $error;
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
    public function resolveCallable(callable $callable) {
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

    protected function resolveUnionType(\ReflectionUnionType $type, \ReflectionParameter $param): mixed {
        /** @var ReflectionNamedType $namedType */
        foreach ($type->getTypes() as $namedType) {

            try {

                $resolved = $this->resolveNamedType($namedType, $param);
                return $resolved;
            } catch (\Throwable) {

            }
        }

        if ($type->allowsNull()) return null;
        throw new RuntimeException();
    }

    protected function resolveNamedType(\ReflectionNamedType $type, \ReflectionParameter $param): mixed {

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

    protected function resolveType(\ReflectionType $type, \ReflectionParameter $param): mixed {


        if ($type instanceof \ReflectionNamedType) {
            return $this->resolveNamedType($type, $param);
        } elseif ($type instanceof \ReflectionUnionType) return $this->resolveUnionType($type, $param);
        elseif ($type->allowsNull()) return null;

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


        $params = $reflection->getParameters();
        /** @var \ReflectionParameter $param */
        foreach ($params as $index => $param) {

            $type = $param->getType();
            $allowsNull = $type === null || $type->allowsNull();
            if ($type === null) {
                try {
                    if ($param->isDefaultValueAvailable()) {
                        // ReflectionException can be thrown here
                        $resolved = $param->getDefaultValue();
                        $result[] = $resolved;
                        continue;
                    } elseif ($allowsNull) {
                        $result[] = null;
                        continue;
                    }
                } catch (ReflectionException) {

                }
                throw new RuntimeException(sprintf('Cannot resolve parameter %u', $index));
            }


            try {
                $result[] = $this->resolveType($type, $param);
            } catch (\Throwable) {

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
