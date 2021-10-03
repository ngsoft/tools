<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\{
    Exceptions\NotFoundException, Traits\ContainerAware
};
use Psr\Container\ContainerInterface,
    ReflectionFunction,
    ReflectionFunctionAbstract,
    ReflectionMethod,
    ReflectionNamedType,
    RuntimeException;

class Resolver {

    use ContainerAware;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Resolves a className using invoker
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
     * Resolves Method Type Hints
     *
     * @param ReflectionFunctionAbstract $reflection
     * @return array
     * @throws RuntimeException
     */
    public function resolveParameters(ReflectionFunctionAbstract $reflection): array {
        $result = [];
        $params = $reflection->getParameters();
        if (count($params) == 0) return $result;

        foreach ($params as $index => $param) {
            $type = $param->getType();
            if (
                    !$type or
                    $type->isBuiltin() or
                    $type instanceof ReflectionNamedType === false
            ) {
                throw new RuntimeException(sprintf('Cannot resolve %u parametter', $index));
            }

            /** @var ReflectionNamedType $type */
            $nullable = $type->allowsNull();

            $className = $type->getName();
            if ($className === 'self') {
                $className = $param->getDeclaringClass()->getName();
            }

            if ($this->container->has($className)) {
                try {
                    $resolved = $this->container->get($className);
                    $result[] = $resolved;
                } catch (NotFoundException $error) {
                    if (!$nullable) throw $error;
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
