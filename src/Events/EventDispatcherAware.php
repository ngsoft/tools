<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use InvalidArgumentException,
    Psr\EventDispatcher\EventDispatcherInterface as PSREventDispatcherInterface,
    Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

/**
 * A Trait that acts as a NullDispatcher or forwards calls to an EventDispatcherInterface
 */
trait EventDispatcherAware {

    /** @var PSREventDispatcherInterface|SymfonyEventDispatcherInterface|null */
    protected $eventDispatcher;

    /**
     * Set an event dispatcher to forwards calls to
     *
     * @param PSREventDispatcherInterface|null $eventDispatcher
     * @return void
     */
    public function setEventDispatcher(?PSREventDispatcherInterface $eventDispatcher): void {
        // prevent infinite loop
        if ($eventDispatcher instanceof self) throw new InvalidArgumentException(sprintf('Cannot forward events to %s.', static::class));
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Dispatches an event to all registered listeners.
     * Convenient method to forward event to registered dispatcher
     *
     * @param object      $event     The event to pass to the event handlers/listeners
     * @param string|null $eventName The name of the event to dispatch. If not supplied,
     *                               the class of $event should be used instead.
     *
     * @return object The passed $event MUST be returned
     */
    public function dispatch(object $event, string $eventName = null): object {
        if (
                $event instanceof StoppableEventInterface and
                $event->isPropagationStopped()
        ) {
            return $event;
        } elseif ($this->eventDispatcher instanceof SymfonyEventDispatcherInterface) {
            return $this->eventDispatcher->dispatch($event, $eventName);
        } elseif ($this->eventDispatcher instanceof PSREventDispatcherInterface) {
            return $this->eventDispatcher->dispatch($event);
        }
        return $event;
    }

}
