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

    protected const RESOLVER_STACK = [
        Resolvers\ParameterResolver::class,
    ];

    public function __construct(array $definitions = [])
    {
        foreach (self::RESOLVER_STACK as $resolver) {
            $this->addResolutionHandler(new $resolver());
        }
        parent::__construct($definitions);
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        $id = $this->handleAliasResolution($id);
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers) || class_exists($id);
    }

    protected function isResolved(string $id): bool
    {
        $this->handleServiceProvidersResolution($id);

        if (array_key_exists($id, $this->definitions)) {
            return $this->definitions[$id] instanceof Closure === false;
        } elseif (class_exists($id)) {
            return false;
        }
        throw new NotFoundException($this, $id);
    }

}
