<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use NGSOFT\Traits\ContainerAware,
    Psr\EventDispatcher\ListenerProviderInterface,
    RuntimeException;

class EventListener implements ListenerProviderInterface {

    use ContainerAware;

    /** @var array */
    private $listeners = [];

    /** @var callable[] */
    private $sorted = [];

    /** {@inheritdoc} */
    public function getListenersForEvent(object $event): iterable {
        foreach ($this->sorted as $eventName => $listener) {
            if ($event instanceof $eventName) {
                yield $eventName;
            }
        }
    }

    /**
     * Add an Event Listener
     *
     * @param string $eventName The Event to listen to
     * @param callable $listener The listener
     * @param int $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     * @return void
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): void {
        $this->listeners[$eventName] = $this->listeners[$eventName] ?? [];
        $this->listeners[$eventName][$priority] = $this->listeners[$eventName][$priority] ?? [];
        $this->listeners[$eventName][$priority][] = $listener;
        $this->sortListeners($eventName);
    }

    /**
     * Remove a registered listener
     *
     * @param string $eventName The Event to listen to
     * @param callable $listener The listener
     * @return void
     */
    public function removeListener(string $eventName, callable $listener): void {
        if (!isset($this->listeners[$eventName])) return;
        foreach ($this->listeners[$eventName] as $priority => &$listeners) {
            $id = array_search($listener, $listeners, true);
            if (false !== $id) unset($listeners[$id], $this->sorted[$eventName]);
            if (count($listeners) == 0) unset($this->listeners[$eventName][$priority]);
        }
    }

    /**
     * Auto register a listener type hinting its first parameter
     *
     * @param callable $listener The listener
     * @param int $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     */
    public function register(callable $listener, int $priority = 0) {
        if ($eventName = $this->autoDetectEventName($listener)) {
            $this->addListener($eventName, $listener, $priority);
        }
    }

    /**
     * Auto unregister a listener type hinting its first parameter
     *
     * @param callable $listener The listener
     */
    public function unregister(callable $listener) {
        if ($eventName = $this->autoDetectEventName($listener)) {
            $this->removeListener($eventName, $listener);
        }
    }

    /**
     * Suscribe to an event using the container
     *
     * @param string $eventName Event to suscribe for
     * @param string $service Container Key to register
     * @param int $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     * @return bool true on success, false otherwise
     * @throws RuntimeException if no container registered
     */
    public function addService(string $eventName, string $service, int $priority): bool {
        if (!$this->getContainer()) {
            throw new RuntimeException('You must add a Container before registering a service.');
        }

        if (!$this->getContainer()->has($service)) {
            return false;
        }

        $priority = max(0, $priority);

        $this->listeners[$eventName] = $this->listeners[$eventName] ?? [];
        $this->listeners[$eventName][$priority] = $this->listeners[$eventName][$priority] ?? [];

        $serviceInstance = $this->getContainer()->get($service);
        if (
                is_callable($serviceInstance) or
                (is_object($serviceInstance) and method_exists($serviceInstance, '__invoke'))
        ) {
            $this->listeners[$eventName][$priority][] = $serviceInstance;
            $this->sortListeners($eventName);
            return true;
        }
        return false;
    }

    /**
     * Auto detect event name using listenet first parameter
     * @param callable $listener
     * @return string
     */
    private function autoDetectEventName(callable $listener): string {
        try {
            $closure = $listener instanceof \Closure ? $listener : \Closure::fromCallable($listener);
            $params = (new \ReflectionFunction($closure))->getParameters();
            if (count($params) == 0) {
                throw new \InvalidArgumentException('Listeners must declare at least one parameter.');
            }
            if ($types = $params[0]->getType()) {
                /** @var \ReflectionNamedType|\ReflectionUnionType $types */
                if (method_exists($types, 'getTypes')) $types = $types->getTypes(); // Union PHP 8 support
                else $types = [$types]; //polyfill
                $rType = count($types) > 0 ? $types[0] : null;
            } else $rType = null;

            if ($rType === null) {
                throw new \InvalidArgumentException('Listeners must declare an object type they can accept.');
            }
            return $rType->getName();
        } catch (ReflectionException $error) {
            throw new \RuntimeException('Type error registering listener.', 0, $e);
        }
    }

    /**
     * Sort listeners by priority
     */
    private function sortListeners(string $eventName) {
        krsort($this->listeners[$eventName]);
        $this->sorted[$eventName] = [];

        foreach ($this->listeners[$eventName] as $listeners) {
            foreach ($listeners as $listener) {
                $this->sorted[$eventName][] = $listener;
            }
        }
    }

}
