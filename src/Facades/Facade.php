<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use BadMethodCallException,
    Closure;
use NGSOFT\Container\{
    Container, ContainerInterface, NullServiceProvider, ServiceProvider
};
use RuntimeException;
use function class_basename;

abstract class Facade
{

    protected static ?ContainerInterface $container = null;
    protected static $resolvedInstance = [];
    protected static $cached = true;

    private function __construct()
    {

    }

    /**
     * Handle dynamic, static calls to the object.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = static::getFacadeRoot();

        if ( ! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        if ( ! method_exists($instance, $method)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $method));
        }

        return $instance->$method(...$args);
    }

    /**
     * Get the registered name of the component.
     */
    abstract protected static function getFacadeAccessor(): string;

    /**
     * Get the service provider for the component
     */
    protected static function getServiceProvider(): ServiceProvider
    {
        return new NullServiceProvider;
    }

    /**
     * Run a Closure when the facade has been resolved.
     *
     * @param  Closure  $callback
     * @return void
     */
    public static function resolved(Closure $callback): void
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
        return new Container();
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
    public static function getFacadeRoot(): mixed
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

        if ( ! static::getContainer()->has(static::getFacadeAccessor())) {
            static::getContainer()->register(static::getServiceProvider());
        }

        if (static::$cached) {
            return static::$resolvedInstance[$name] = static::getContainer()->get($name);
        }

        return static::getContainer()->get($name);
    }

    /**
     * Clear a resolved facade instance.
     *
     * @param  string  $name
     * @return void
     */
    public static function clearResolvedInstance(string $name): void
    {
        unset(static::$resolvedInstance[$name]);
    }

    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances(): void
    {
        static::$resolvedInstance = [];
    }

}
