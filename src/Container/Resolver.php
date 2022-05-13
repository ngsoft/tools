<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\{
    Exceptions\NotFoundException, Traits\ContainerAware
};
use Psr\Container\ContainerInterface,
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

    use ContainerAware;

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
                // ContainerAware
                if (method_exists($result, 'setContainer')) $result->setContainer($this->container);
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
            if (is_object($result) and method_exists($result, 'setContainer')) $result->setContainer($this->container);
        } catch (RuntimeException $error) {
            if ($error instanceof NotFoundException) throw $error;
        }
        return $result;
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

            if (
                    !$type or
                    $type instanceof ReflectionNamedType === false or
                    $type->isBuiltin()
            ) {
                try {
                    if ($param->isDefaultValueAvailable()) {
                        // ReflectionException can be thrown here
                        $resolved = $param->getDefaultValue();
                        $result[] = $resolved;
                        continue;
                    } elseif (
                            $type instanceof ReflectionType
                            and $type->allowsNull()
                    ) {
                        $result[] = null;
                        continue;
                    }
                } catch (ReflectionException $error) {

                }
                throw new RuntimeException(sprintf('Cannot resolve %u parameter', $index));
            }

            /** @var ReflectionNamedType $type */
            $className = $type->getName();
            if ($className === 'self') {
                $className = $param->getDeclaringClass()->getName();
            }

            if ($this->container->has($className)) {
                try {
                    $resolved = $this->container->get($className);
                    $result[] = $resolved;
                } catch (NotFoundException $error) {
                    if (!$type->allowsNull()) throw $error;
                    $result[] = null;
                }
            }
        }
        return $result;
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        return [];
    }

}
