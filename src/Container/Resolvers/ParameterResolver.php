<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use Closure;
use NGSOFT\Container\{
    ContainerInterface, ContainerResolver, ContainerResolverException, NotFoundException
};
use Psr\Container\ContainerExceptionInterface,
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
class ParameterResolver implements ContainerResolver
{

    protected ContainerInterface $container;
    protected array $errors = [];

    public function __invoke(ContainerInterface $container, string $id, mixed $value): mixed
    {
        $this->container = $container;
        $this->errors = [];
        return $this->resolve($id, $value);
    }

    public function resolve(string $id, mixed $entry): mixed
    {
        $resolved = null;
        try {
            if (is_null($entry)) {
                if ($reflectionClass = $this->resolveClassName($id)) {

                    if ($reflectionClass->isInstantiable()) {
                        if ($constructor = $reflectionClass->getConstructor()) {
                            $resolved = $reflectionClass->newInstance(...$this->resolveParameters($id, $constructor));
                        } else {
                            $resolved = $reflectionClass->newInstanceWithoutConstructor();
                        }
                    } else throw new ContainerResolverException(sprintf('Entry "%s" cannot be instanciated.', $id));
                }
            } elseif ($entry instanceof Closure) {

                $resolved = call_user_func_array($entry, $this->resolveParameters($id, new ReflectionFunction($entry)));
                if (is_object($resolved)) {
                    $resolved = $resolved;
                } elseif (is_null($resolved)) {
                    throw new ContainerResolverException(sprintf('Entry "%s" {closure} does not return any value.', $id));
                }
            } else $resolved = $entry;
        } catch (Throwable $error) {
            throw new NotFoundException($this->container, $id, $error);
        }
        //if ($resolved === null) throw new NotFoundException($this->container, $id);
        return $resolved;
    }

    public function resolveParameters(string $id, ReflectionMethod|ReflectionFunction $reflectionMethod): array
    {
        if ($reflectionMethod->getNumberOfParameters() === 0) return [];

        $result = [];

        /** @var ReflectionParameter $reflectionParameter */
        foreach ($reflectionMethod->getParameters() as $index => $reflectionParameter) {

            $reflectionType = $reflectionParameter->getType();

            if ($reflectionType === null) {
                if ($reflectionParameter->isDefaultValueAvailable()) $result[] = $reflectionParameter->getDefaultValue();
                else $result[] = $index === 0 ? $this->container : null;
                continue;
            }

            $result[] = $this->resolveType($id, $reflectionType, $reflectionParameter);
        }

        return $result;
    }

    public function resolveType(string $id, ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType $reflectionType, ReflectionParameter $reflectionParameter): mixed
    {

        if ($reflectionType instanceof ReflectionIntersectionType) {
            throw new ContainerResolverException('Intersection types are not allowed by this container.');
        } elseif ($reflectionType instanceof ReflectionUnionType) $reflectionTypes = $reflectionType->getTypes();
        else $reflectionTypes = [$reflectionType];


        $previous = null;
        foreach ($reflectionTypes as $reflectionNamedType) {

            $type = $reflectionNamedType->getName();

            if ($reflectionNamedType->isBuiltin()) {
                if ($reflectionParameter->isDefaultValueAvailable()) return $reflectionParameter->getDefaultValue();
            } elseif (in_array($type, ['static', 'self'])) {
                //we try to resolve that class so => infinite loop
                continue;
            } else {

                try {
                    $value = $this->container->get($type);

                    if ( ! is_a($value, $type)) {
                        $previous = new ContainerResolverException(sprintf(
                                        'Container returned type %s instead of type %s for entry %s',
                                        get_debug_type($value), $type, $id
                                ), 0, $previous);
                    } else { return $value; }
                } catch (ContainerExceptionInterface) {

                }
            }
        }

        if ($reflectionParameter->allowsNull()) {
            return null;
        }

        throw new ContainerResolverException(sprintf(
                                'Cannot resolve entry "%s" %s parameter #%d "%s" of type %s',
                                $id,
                                $reflectionParameter->getDeclaringFunction()->getName(),
                                $reflectionParameter->getPosition(),
                                $reflectionParameter->getName(),
                                $reflectionType
                        ), 0, $previous);
    }

    public function resolveClassName(string $className): ?ReflectionClass
    {
        return class_exists($className) ? new ReflectionClass($className) : null;
    }

}
