<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\Container\{
    Resolvers\ContainerResolver, ServiceProvider
};

class Container extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return static::getAlias();
    }

    /**
     * Alias an entry to a different name
     */
    public static function alias(array|string $alias, string $id): void
    {
        static::getFacadeRoot()->alias($alias, $id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     */
    public static function has(string $id): bool
    {
        return static::getFacadeRoot()->has($id);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     */
    public static function get(string $id): mixed
    {
        return static::getFacadeRoot()->get($id);
    }

    /**
     * Resolves an entry by its name. If given a class name, it will return a fresh instance of that class.
     */
    public static function make(string $id, array $parameters = []): mixed
    {
        return static::getFacadeRoot()->make($id, $parameters);
    }

    /**
     * Call the given function using the given parameters.
     */
    public static function call(object|array|string $callable, array $parameters = []): mixed
    {
        return static::getFacadeRoot()->call($callable, $parameters);
    }

    /**
     * Register a service
     */
    public static function register(ServiceProvider $service): void
    {
        static::getFacadeRoot()->register($service);
    }

    /**
     * Add a definition to the container
     */
    public static function set(string $id, mixed $value): void
    {
        static::getFacadeRoot()->set($id, $value);
    }

    /**
     * Adds multiple definitions
     */
    public static function setMany(iterable $definitions): void
    {
        static::getFacadeRoot()->setMany($definitions);
    }

    /**
     * Adds an handler to manage entry resolution (after params have been resolved)
     */
    public static function addContainerResolver(ContainerResolver $resolver, ?int $priority = null): void
    {
        static::getFacadeRoot()->addContainerResolver($resolver, $priority);
    }

}
