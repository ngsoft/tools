<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Invoker\ParameterResolver\Container\TypeHintContainerResolver,
    NGSOFT\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface,
    ReflectionMethod,
    ReflectionNamedType,
    RuntimeException;

class Resolver {

    /** @var ContainerInterface */
    private $container;

    /** @var TypeHintContainerResolver */
    private $typeHintResolver;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->typeHintResolver = new TypeHintContainerResolver($container);
    }

    /**
     * Resolves a className using invoker
     *
     * @param string $className
     * @return ?object
     */
    public function resolve(string $className): ?object {

        if (class_exists($className)) {
            if (!method_exists($className, '__construct')) return new $className();
            $reflection = new ReflectionMethod($className, '__construct');
            try {
                $args = $this->resolveParameters($reflection);
                return new $className(...$args);
            } catch (RuntimeException $error) {
                if ($error instanceof NotFoundException) throw $error;
                return null;
            }
        }

        return null;
    }

    /**
     * Resolves Method Type Hints
     *
     * @param \ReflectionMethod $reflection
     * @return array
     * @throws RuntimeException
     */
    public function resolveClassParameters(\ReflectionMethod $reflection): array {
        $result = [];
        $params = $reflection->getParameters();
        if (count($params) == 0) return $result;



        foreach ($params as $param) {
            $type = $param->getType();
            if (
                    !$type or
                    $type->isBuiltin() or
                    $type instanceof ReflectionNamedType === false
            ) {
                throw new RuntimeException(sprintf('Cannot resolve classname: %s', $reflection->getDeclaringClass()->getName()));
            }

            $nullable = $type->allowsNull();

            $className = $type->getName();
            if ($className === 'self') {
                $className = $parameter->getDeclaringClass()->getName();
            }

            if ($this->container->has($parameterClass)) {
                try {
                    $resolved = $this->container->get($parameterClass);
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
