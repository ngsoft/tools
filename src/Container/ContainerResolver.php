<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\Exceptions\{
    ContainerResolverException, NotFoundException
};
use Psr\Container\{
    ContainerExceptionInterface, ContainerInterface
};
use ReflectionClass,
    ReflectionException,
    ReflectionFunction,
    ReflectionIntersectionType,
    ReflectionMethod,
    ReflectionNamedType,
    ReflectionParameter,
    ReflectionUnionType;

/**
 * @phan-file-suppress PhanTypeMismatchArgumentSuperType
 */
class ContainerResolver
{

    public function resolve(string $key, mixed $entry, ContainerInterface $container): mixed
    {
        $resolved = $error = null;
        try {
            if (is_null($entry)) {
                if ($reflectionClass = $this->resolveClassName($key)) {

                    if ($reflectionClass->isInstantiable()) {
                        if ($constructor = $reflectionClass->getConstructor()) {
                            if ($params = $this->resolveParameters($constructor, $container)) { $resolved = $reflectionClass->newInstance(...$params); }
                        } else {
                            return $reflectionClass->newInstanceWithoutConstructor();
                        }
                    }
                }
            } elseif ($entry instanceof Closure) {
                if ($params = $this->resolveParameters(new ReflectionFunction($entry), $container)) { $resolved = call_user_func_array($entry, $params); }
            } else $resolved = $entry;
        } catch (\Throwable $error) {
            throw new NotFoundException($container, $key, $error);
        }
        if ($resolved === null) throw new NotFoundException($container, $key);
        return $resolved;
    }

    private function resolveParameters(ReflectionMethod|ReflectionFunction $reflectionMethod, ContainerInterface $container): array
    {
        if ($reflectionMethod->getNumberOfParameters() === 0) return [];

        $result = [];

        /** @var ReflectionParameter $reflectionParameter */
        foreach ($reflectionMethod->getParameters() as $index => $reflectionParameter) {

            $reflectionType = $reflectionParameter->getType();

            if ($reflectionType === null) {
                if ($reflectionParameter->isDefaultValueAvailable()) $result[] = $reflectionParameter->getDefaultValue();
                else $result[] = $index === 0 ? $container : null;
                continue;
            }

            $result[] = $this->resolveType($reflectionType, $reflectionParameter, $container);
        }

        return $result;
    }

    private function resolveType(ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType $reflectionType, ReflectionParameter $reflectionParameter, ContainerInterface $container): mixed
    {

        if ($reflectionType instanceof ReflectionIntersectionType) {
            throw new ContainerResolverException('Intersection types are not allowed by this container.');
        } elseif ($reflectionType instanceof ReflectionIntersectionType) $reflectionType = $reflectionType->getTypes();
        else $reflectionType = [$reflectionType];

        foreach ($reflectionType as $reflectionNamedType) {
            $type = $reflectionNamedType->getName();

            if (
                    $reflectionNamedType->isBuiltin() &&
                    $reflectionParameter->isDefaultValueAvailable()
            ) {
                return $reflectionParameter->getDefaultValue();
            } elseif (in_array($type, ['static', 'self'])) {
                //we try to resolve that class so => infinite loop
                continue;
            } else {
                return $container->get($type);
            }
        }

        if ($reflectionParameter->allowsNull()) {
            return null;
        }

        throw new ContainerResolverException(sprintf('Cannot resolve parameter %d', $reflectionParameter->getPosition()), 0, $error);
    }

    private function resolveClassName(string $className): ?ReflectionClass
    {
        return class_exists($className) ? new ReflectionClass($className) : null;
    }

}
