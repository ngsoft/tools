<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use Closure,
    NGSOFT\Container\ContainerInterface,
    RuntimeException;
use function class_basename;

abstract class Facade
{

    protected static ?ContainerInterface $container = null;
    protected static $resolvedInstance = [];
    protected static $cached = true;

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     *
     * @throws RuntimeException
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = static::getFacadeRoot();

        if ( ! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    abstract protected static function getFacadeAccessor(): string;

    /**
     * Run a Closure when the facade has been resolved.
     *
     * @param  Closure  $callback
     * @return void
     */
    public static function resolved(Closure $callback)
    {
        $accessor = static::getFacadeAccessor();

        if (static::getContainer()->has($accessor) === true) {
            $callback(static::getFacadeRoot());
        }
    }

    public static function swap(mixed $instance): void
    {
        static::$resolvedInstance[static::getFacadeAccessor()] = $instance;
        static::getContainer()->set(static::getFacadeAccessor(), $instance);
    }

    protected static function getAlias(): string
    {
        return class_basename(static::class);
    }

    public static function getContainer(): ContainerInterface
    {
        if ( ! static::$container) {
            static::$container = static::startContainer();
        }
        return static::$container;
    }

    protected static function startContainer(): ContainerInterface
    {
        return new \NGSOFT\Container\Container();
    }

    public static function setContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string  $name
     * @return mixed
     */
    protected static function resolveFacadeInstance(string $name): mixed
    {
        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        if (static::$cached) {
            return static::$resolvedInstance[$name] = static::getContainer()->get($name);
        }

        return static::getContainer()->get($name);

        return null;
    }

    /**
     * Clear a resolved facade instance.
     *
     * @param  string  $name
     * @return void
     */
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstance[$name]);
    }

    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = [];
    }

}
