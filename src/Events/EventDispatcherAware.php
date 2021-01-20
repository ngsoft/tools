<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use InvalidArgumentException;
use Psr\EventDispatcher\{
    EventDispatcherInterface as PSREventDispatcherInterface, StoppableEventInterface
};
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

/**
 * A Trait that acts as a NullDispatcher or forwards calls to an EventDispatcher
 * Add support to PSR-14 to any class
 */
trait EventDispatcherAware {

    /**
     * Configured Event Dispatcher to forward calls to
     * @var PSREventDispatcherInterface|SymfonyEventDispatcherInterface|null
     */
    private $eventDispatcher;

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

    protected function getEventDispatcher(): ?PSREventDispatcherInterface {
        return $this->eventDispatcher;
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
        $real = $this->getEventDispatcher();
        if (
                $event instanceof StoppableEventInterface and
                $event->isPropagationStopped()
        ) {
            return $event;
        } elseif ($real instanceof SymfonyEventDispatcherInterface) {
            return $real->dispatch($event, $eventName);
        } elseif ($real instanceof PSREventDispatcherInterface) {
            return $real->dispatch($event);
        }
        return $event;
    }

}
