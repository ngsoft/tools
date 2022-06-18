<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use ReflectionClass,
    ReflectionFunction,
    ReflectionIntersectionType,
    ReflectionMethod,
    ReflectionNamedType,
    ReflectionParameter,
    ReflectionUnionType,
    Throwable;

/**
 * Container that supports autowiring for dependency injection
 *
 * @phan-file-suppress PhanTypeMismatchArgumentSuperType
 */
class Container extends ContainerAbstract
{

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers) || class_exists($id);
    }

    protected function isResolved(string $id): bool
    {
        $this->handleServiceProvidersResolution($id);

        if ($this->has($id)) {

            return ($this->definitions[$id] ?? null) instanceof Closure === false || class_exists($id);
        }


        throw new NotFoundException($this, $id);
    }

}
