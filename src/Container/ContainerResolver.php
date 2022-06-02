<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\Exceptions\{
    ContainerResolverException, NotFoundException
};
use Psr\Container\ContainerInterface,
    ReflectionClass,
    ReflectionFunction,
    ReflectionIntersectionType,
    ReflectionMethod,
    ReflectionNamedType,
    ReflectionParameter,
    ReflectionUnionType,
    Throwable;

/**
 * @phan-file-suppress PhanTypeMismatchArgumentSuperType
 */
class ContainerResolver
{

    protected $errors = [];

    public function resolve(string $id, mixed $entry, ContainerInterface $container): mixed
    {
        $resolved = null;
        $this->errors = [];
        try {
            if (is_null($entry)) {
                if ($reflectionClass = $this->resolveClassName($id)) {

                    if ($reflectionClass->isInstantiable()) {
                        if ($constructor = $reflectionClass->getConstructor()) {
                            if ($params = $this->resolveParameters($id, $constructor, $container)) { $resolved = $reflectionClass->newInstance(...$params); }
                        } else {
                            return $reflectionClass->newInstanceWithoutConstructor();
                        }
                    }
                }
            } elseif ($entry instanceof Closure) {
                if ($params = $this->resolveParameters($id, new ReflectionFunction($entry), $container)) { $resolved = call_user_func_array($entry, $params); }
            } else $resolved = $entry;
        } catch (Throwable $error) {
            throw new NotFoundException($container, $id, $error);
        }
        if ($resolved === null) throw new NotFoundException($container, $id);
        return $resolved;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function resolveParameters(string $id, ReflectionMethod|ReflectionFunction $reflectionMethod, ContainerInterface $container): array
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

            $result[] = $this->resolveType($id, $reflectionType, $reflectionParameter, $container);
        }

        return $result;
    }

    private function resolveType(string $id, ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType $reflectionType, ReflectionParameter $reflectionParameter, ContainerInterface $container): mixed
    {

        if ($reflectionType instanceof ReflectionIntersectionType) {
            throw new ContainerResolverException('Intersection types are not allowed by this container.');
        } elseif ($reflectionType instanceof \ReflectionUnionType) $reflectionTypes = $reflectionType->getTypes();
        else $reflectionTypes = [$reflectionType];

        foreach ($reflectionTypes as $reflectionNamedType) {

            $type = $reflectionNamedType->getName();

            if (
                    $reflectionNamedType->isBuiltin()
            ) {
                if ($reflectionParameter->isDefaultValueAvailable()) return $reflectionParameter->getDefaultValue();
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

        throw new ContainerResolverException(sprintf('Cannot resolve entry "%s" parameter #%d of type %s', $id, $reflectionParameter->getPosition(), $reflectionType));
    }

    private function resolveClassName(string $className): ?ReflectionClass
    {
        return class_exists($className) ? new ReflectionClass($className) : null;
    }

}
