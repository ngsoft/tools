<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use Psr\{
    Container\ContainerInterface, EventDispatcher\ListenerProviderInterface
};
use RuntimeException;

class EventListener implements ListenerProviderInterface {

    use ParameterDeriverTrait;

    /** @var ?ContainerInterface */
    private $container;

    /** @var callable[] */
    private $listeners = [];

    public function getListenersForEvent(object $event): iterable {

    }

    /**
     * Suscribe to an event using the container
     *
     * @param string $eventName Event to suscribe for
     * @param string $service Container Key to register
     * @return bool true on success, false otherwise
     * @throws RuntimeException
     */
    public function addService(string $eventName, string $service): bool {
        if (!$this->container) {
            throw new RuntimeException('You must add a Container before registering a service.');
        }
        $this->listeners[$eventName] = $this->listeners[$eventName] ?? [];
        if (!$this->container->has($service)) {
            return false;
        }
        $serviceInstance = $this->container->get($service);
        if (
                is_callable($serviceInstance) or
                (is_object($serviceInstance) and method_exists($serviceInstance, '__invoke'))
        ) {
            $this->listeners[$eventName][] = $serviceInstance;
            return true;
        }
        return false;
    }

    /**
     * Set a Container to add Services
     *
     * @param ContainerInterface|null $container
     * @return void
     */
    public function setContainer(?ContainerInterface $container): void {
        $this->container = $container;
    }

}
