<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use Psr\EventDispatcher\{
    EventDispatcherInterface, ListenerProviderInterface, StoppableEventInterface
};

/**
 * Event Dispatcher that forwards call to another dispatcher,
 * or dispatches event to a set listener
 * or just returns the event (Null Dispatcher)
 */
final class EventDispatcher implements EventDispatcherInterface {

    use EventDispatcherAware;

    /** @var ?ListenerProviderInterface */
    private $eventListener;

    /**
     * Support for autowiring
     * @param ?ListenerProviderInterface $eventListener Event Listener to listen to
     */
    public function __construct(
            ListenerProviderInterface $eventListener = null
    ) {
        if ($eventListener) $this->setEventListener($eventListener);
    }

    /**
     * Returns the set EventListener
     *
     * @return ListenerProviderInterface|null
     */
    public function getEventListener(): ?ListenerProviderInterface {
        return $this->eventListener;
    }

    /**
     * Set an Event Listener to dispatch events to
     *
     * @param ListenerProviderInterface|null $eventListener
     */
    public function setEventListener(?ListenerProviderInterface $eventListener) {
        $this->eventListener = $eventListener;
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param object $event     The event to pass to the event handlers/listeners
     *
     * @return object The passed $event MUST be returned
     */
    public function dispatch(object $event) {
        if (
                ($stoppable = $event instanceof StoppableEventInterface) and
                $event->isPropagationStopped()
        ) {
            return $event;
        }


        if ($this->getEventDispatcher() instanceof EventDispatcherInterface) {
            return $this->getEventDispatcher()->dispatch($event);
        }


        if ($this->eventListener instanceof ListenerProviderInterface) {

            foreach ($this->eventListener->getListenersForEvent($event) as $call) {
                if ($stoppable and $event->isPropagationStopped()) break;
                $call($event);
            }
        }

        return $event;
    }

}
