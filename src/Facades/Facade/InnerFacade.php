<?php

declare(strict_types=1);

namespace NGSOFT\Facades\Facade;

use NGSOFT\{
    Container\ContainerInterface, Container\ServiceProvider, Facades\Facade
};
use function implements_class,
             NGSOFT\Filesystem\require_all_once;

final class InnerFacade extends Facade
{

    protected array $resovedInstances = [];
    protected ?ContainerInterface $container = null;
    private array $providers = [];

    /**
     * Starts the container
     */
    final public function boot(array $definitions = []): void
    {

        if ( ! $this->container) {
            $this->getContainer()->setMany($definitions);
        }
    }

    final public function registerServiceProvider(string $accessor, ServiceProvider $provider): void
    {
        if ( ! isset($this->providers[$accessor])) {
            $this->providers[$accessor] = $provider;
            $this->getContainer()->register($provider);
        }
    }

    protected function getNewContainer(): ContainerInterface
    {
        $class = self::DEFAULT_CONTAINER_CLASS;
        return $this->registerFacades(new $class());
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
        return $this->container ??= $this->getNewContainer();
    }

    private function registerFacades(ContainerInterface $container): ContainerInterface
    {
        if (empty($this->providers)) {
            require_all_once(dirname(__DIR__));
            foreach (implements_class(Facade::class, false) as $class) {


                if ($class === __CLASS__ || $class === Facade::class) {
                    continue;
                }
                $accessor = $class::getFacadeAccessor();

                $this->providers[$accessor] = $class::getServiceProvider();
            }
        }


        foreach ($this->providers as $provider) {
            $container->register($provider);
        }

        return $container;
    }

    final public function setContainer(ContainerInterface $container): void
    {
        $this->container = $this->registerFacades($container);
    }

    protected static function getFacadeAccessor(): string
    {
        return 'Facade';
    }

}
