<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use BadMethodCallException;
use NGSOFT\Container\{
    Container, ContainerInterface, NullServiceProvider, ServiceProvider
};
use RuntimeException;
use function class_basename;

abstract class Facade
{

    protected const DEFAULT_CONTAINER_CLASS = Container::class;

    private static ?Facade $innerFacade = null;

    ////////////////////////////   Overrides   ////////////////////////////

    /**
     * Get the registered name of the component.
     */
    abstract protected static function getFacadeAccessor(): string;

    /**
     * Get the service provider for the component
     */
    protected static function getServiceProvider(): ServiceProvider
    {
        return new NullServiceProvider();
    }

    /**
     * Indicates if the resolved instance should be cached.
     */
    protected static function isCached(): bool
    {
        return true;
    }

    /**
     * Returns the class basename of the facade
     */
    protected static function getAlias(): string
    {
        return class_basename(static::class);
    }

    ////////////////////////////   Utilities   ////////////////////////////

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    final public static function getFacadeRoot(): mixed
    {

        if (static::class === __CLASS__) {
            return self::getInnerFacade();
        }

        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Get The facade instance
     *
     * @return Facade
     */
    final protected static function getInnerFacade(): self
    {
        /**
         * we extends the facade as it is abstract
         * with that we can Facade::setContainer() without static error
         */
        return self::$innerFacade = self::$innerFacade ?? new class extends Facade{

                    protected array $resovedInstances = [];
                    protected ?ContainerInterface $container = null;

                    final public function registerServiceProvider(ServiceProvider $provider): void
                    {
                        $this->getContainer()->register($provider);
                    }

                    protected function getNewContainer(): ContainerInterface
                    {
                        $class = self::DEFAULT_CONTAINER_CLASS;
                        return new $class();
                    }

                    final public function getResovedInstance(string $name): mixed
                    {
                        return $this->resovedInstances[$name] ?? null;
                    }

                    final public function setResolvedInstance(string $name, object $instance): void
                    {
                        $this->resovedInstances[$name] = $instance;
                    }

                    final public function getContainer(): ContainerInterface
                    {
                        return $this->container = $this->container ?? $this->getNewContainer();
                    }

                    final public function setContainer(ContainerInterface $container): void
                    {
                        $this->container = $container;
                    }

                    protected static function getFacadeAccessor(): string
                    {
                        return 'Facade';
                    }
                };
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @phan-suppress PhanUndeclaredMethod
     * @param  string  $name
     * @return mixed
     */
    final protected static function resolveFacadeInstance(string $name): mixed
    {

        static $providers = [];

        $facade = self::getInnerFacade();

        $resolved = $facade->getResovedInstance($name);

        if ( ! is_null($resolved)) {
            return $resolved;
        }


        if ( ! isset($providers[$name])) {
            $provider = $providers[$name] = static::getServiceProvider();
            $facade->registerServiceProvider($provider);
        }


        if ($resolved = $facade->getContainer()->get($name)) {
            static::isCached() && $facade->setResolvedInstance($name, $resolved);
        }

        return $resolved;
    }

    /**
     * Handle dynamic, static calls to the object.
     */
    final public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = static::getFacadeRoot();

        if ( ! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        if ( ! method_exists($instance, $name)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name));
        }

        return $instance->$name(...$arguments);
    }

    protected function __construct()
    {
        // Cannot instanciate except for Facade
    }

}
