<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use InvalidArgumentException;
use Psr\EventDispatcher\{
    EventDispatcherInterface as PSREventDispatcherInterface, StoppableEventInterface
};

/**
 * A Trait that acts as a NullDispatcher or forwards calls to an EventDispatcher
 * Add support to PSR-14 to any class
 */
trait EventDispatcherAware {

    /**
     * Configured Event Dispatcher to forward calls to
     * @var ?PSREventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Set an event dispatcher to forwards calls to
     *
     * @param PSREventDispatcherInterface $eventDispatcher
     * @return void
     */
    public function setEventDispatcher(PSREventDispatcherInterface $eventDispatcher): void {
        // prevent infinite loop
        if ($eventDispatcher instanceof self) throw new InvalidArgumentException(sprintf('Cannot forward events to %s.', static::class));
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get the proxied EventDispatcher
     *
     * @return PSREventDispatcherInterface|null
     */
    protected function getEventDispatcher(): ?PSREventDispatcherInterface {
        return $this->eventDispatcher;
    }

    /**
     * Convenience method to forward event to registered dispatcher
     *
     * @param object      $event     The event to pass to the event handlers/listeners
     *
     * @return object The passed $event MUST be returned
     */
    public function dispatch(object $event) {
        if (
                $event instanceof StoppableEventInterface and
                $event->isPropagationStopped()
        ) {
            return $event;
        } elseif ($this->getEventDispatcher() instanceof PSREventDispatcherInterface) {
            return $this->getEventDispatcher()->dispatch($event);
        }
        return $event;
    }

}
