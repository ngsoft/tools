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
class DIContainer implements Container
{

    public function __construct(
            protected array $definitions = []
    )
    {
        $this->definitions[ContainerInterface::class] = $this->definitions[__CLASS__] = $this;
    }

    public function set(string $id, mixed $entry): void
    {
        $this->definitions[$id] = $entry;
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        if (!$this->isResolved($id)) $this->definitions[$id] = $this->resolve($id, $this->definitions[$id] ?? null);
        return $this->definitions[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || class_exists($id);
    }

    protected function isResolved(string $id): bool
    {
        if (array_key_exists($id, $this->definitions)) {
            return $this->definitions[$id] instanceof Closure === false;
        } elseif (class_exists($id)) {
            return false;
        }
        throw new NotFoundException($this, $id);
    }

    protected function resolve(string $id, mixed $entry): mixed
    {
        $resolved = null;
        try {
            if (is_null($entry)) {
                if ($reflectionClass = $this->resolveClassName($id)) {

                    if ($reflectionClass->isInstantiable()) {
                        if ($constructor = $reflectionClass->getConstructor()) {
                            if ($params = $this->resolveParameters($id, $constructor)) { $resolved = $reflectionClass->newInstance(...$params); }
                        } else {
                            return $reflectionClass->newInstanceWithoutConstructor();
                        }
                    } else throw new ContainerResolverException(sprintf('Entry "%s" cannot be instanciated.', $id));
                }
            } elseif ($entry instanceof Closure) {
                $resolved = call_user_func_array($entry, $this->resolveParameters($id, new ReflectionFunction($entry)));
            } else $resolved = $entry;
        } catch (Throwable $error) {
            throw new NotFoundException($this, $id, $error);
        }
        if ($resolved === null) throw new NotFoundException($this, $id);
        return $resolved;
    }

    protected function resolveParameters(string $id, ReflectionMethod|ReflectionFunction $reflectionMethod): array
    {
        if ($reflectionMethod->getNumberOfParameters() === 0) return [];

        $result = [];

        /** @var ReflectionParameter $reflectionParameter */
        foreach ($reflectionMethod->getParameters() as $index => $reflectionParameter) {

            $reflectionType = $reflectionParameter->getType();

            if ($reflectionType === null) {
                if ($reflectionParameter->isDefaultValueAvailable()) $result[] = $reflectionParameter->getDefaultValue();
                else $result[] = $index === 0 ? $this : null;
                continue;
            }

            $result[] = $this->resolveType($id, $reflectionType, $reflectionParameter);
        }

        return $result;
    }

    protected function resolveType(string $id, ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType $reflectionType, ReflectionParameter $reflectionParameter): mixed
    {

        if ($reflectionType instanceof ReflectionIntersectionType) {
            throw new ContainerResolverException('Intersection types are not allowed by this container.');
        } elseif ($reflectionType instanceof ReflectionUnionType) $reflectionTypes = $reflectionType->getTypes();
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
                return $this->get($type);
            }
        }

        if ($reflectionParameter->allowsNull()) {
            return null;
        }

        throw new ContainerResolverException(sprintf('Cannot resolve entry "%s" %s parameter #%d of type %s', $id, $reflectionParameter->getDeclaringFunction(), $reflectionParameter->getPosition(), $reflectionType));
    }

    protected function resolveClassName(string $className): ?ReflectionClass
    {
        return class_exists($className) ? new ReflectionClass($className) : null;
    }

    public function __debugInfo(): array
    {
        return array_keys($this->definitions);
    }

}
