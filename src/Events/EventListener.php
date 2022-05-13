<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use Closure,
    InvalidArgumentException,
    NGSOFT\Traits\ContainerAware,
    Psr\EventDispatcher\ListenerProviderInterface,
    ReflectionException,
    ReflectionFunction,
    RuntimeException;

/**
 * A Basic Event Listener to use if none available
 */
final class EventListener implements ListenerProviderInterface {

    use ContainerAware;

    /** @var array */
    private $listeners = [];

    /** @var callable[] */
    private $sorted = [];

    /** {@inheritdoc} */
    public function getListenersForEvent(object $event): iterable {
        foreach ($this->sorted as $type => $listeners) {

            if (is_a($event, $type)) {
                foreach ($listeners as $listener) {
                    yield $listener;
                }
            }
        }
    }

    ////////////////////////////   Aliases (JS Like)   ////////////////////////////

    /**
     * Add an Event Listener
     *
     * @param string $eventType The Event Class to listen to
     * @param callable $listener The listener
     * @param int $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     * @return void
     */
    public function on(string $eventType, callable $listener, int $priority = 0): void {
        $this->addListener($eventType, $listener, $priority);
    }

    /**
     * Remove a registered listener
     *
     * @param string $eventType The Event Class to listen to
     * @param callable $listener The listener
     * @return void
     */
    public function off(string $eventType, callable $listener): void {
        $this->removeListener($eventType, $listener);
    }

    ////////////////////////////   API   ////////////////////////////

    /**
     * Add an Event Listener
     *
     * @param string $eventType The Event Class to listen to
     * @param callable $listener The listener
     * @param int $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     * @return void
     */
    public function addListener(string $eventType, callable $listener, int $priority = 0): void {
        $priority = max(0, $priority);
        $this->listeners[$eventType] = $this->listeners[$eventType] ?? [];
        $this->listeners[$eventType][$priority] = $this->listeners[$eventType][$priority] ?? [];
        $this->listeners[$eventType][$priority][] = $listener;
        $this->sortListeners($eventType);
    }

    /**
     * Remove a registered listener
     *
     * @param string $eventType The Event Class to listen to
     * @param callable $listener The listener
     * @return void
     */
    public function removeListener(string $eventType, callable $listener): void {
        if (!isset($this->listeners[$eventType])) return;
        foreach ($this->listeners[$eventType] as $priority => &$listeners) {
            $id = array_search($listener, $listeners, true);
            if (false !== $id) unset($listeners[$id]);
            if (count($listeners) == 0) unset($this->listeners[$eventType][$priority]);
        }
        $this->sortListeners($eventType);
    }

    /**
     * Auto register a listener type hinting its first parameter
     *
     * @param callable $listener The listener
     * @param int $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     */
    public function register(callable $listener, int $priority = 0) {
        if ($eventType = $this->autoDetectEventName($listener)) {
            $this->addListener($eventType, $listener, $priority);
        }
    }

    /**
     * Auto unregister a listener type hinting its first parameter
     *
     * @param callable $listener The listener
     */
    public function unregister(callable $listener) {
        if ($eventType = $this->autoDetectEventName($listener)) {
            $this->removeListener($eventType, $listener);
        }
    }

    /**
     * Suscribe to an event using the container
     *
     * @param string $eventType Event to suscribe for
     * @param string $service Container Key to register
     * @param int $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     * @return bool true on success, false otherwise
     * @throws RuntimeException if no container registered
     */
    public function addService(string $eventType, string $service, int $priority): bool {
        if (!$this->getContainer()) {
            throw new RuntimeException('You must add a Container before registering a service.');
        }

        if (!$this->getContainer()->has($service)) {
            return false;
        }

        $serviceInstance = $this->getContainer()->get($service);
        if (
                is_callable($serviceInstance)
        ) {
            $this->addListener($eventType, $serviceInstance, $priority);
            return true;
        }
        return false;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Auto detect event name using listener first parameter
     *
     * @suppress PhanUndeclaredMethod
     * @param callable $listener
     * @return string
     */
    private function autoDetectEventName(callable $listener): string {
        try {
            $closure = $listener instanceof Closure ? $listener : Closure::fromCallable($listener);
            $params = (new ReflectionFunction($closure))->getParameters();
            if (count($params) == 0) {
                throw new InvalidArgumentException('Listeners must declare at least one parameter.');
            }
            if ($types = $params[0]->getType()) {
                /** @var \ReflectionNamedType|\ReflectionUnionType $types */
                if (method_exists($types, 'getTypes')) $types = $types->getTypes(); // Union PHP 8 support
                else $types = [$types]; //polyfill
                $rType = count($types) > 0 ? $types[0] : null;
            } else $rType = null;

            if (!($rType instanceof \ReflectionNamedType)) {
                throw new InvalidArgumentException('Listeners must declare an object type they can accept.');
            }
            return $rType->getName();
        } catch (ReflectionException $error) {
            throw new \RuntimeException('Type error registering listener.', 0, $error);
        }
    }

    /**
     * Sort listeners by priority
     */
    private function sortListeners(string $eventType) {
        if (!isset($this->listeners[$eventType])) {
            unset($this->sorted[$eventType]);
            return;
        }
        krsort($this->listeners[$eventType]);
        $this->sorted[$eventType] = [];

        foreach ($this->listeners[$eventType] as $listeners) {
            foreach ($listeners as $listener) {
                $this->sorted[$eventType][] = $listener;
            }
        }
    }

}
