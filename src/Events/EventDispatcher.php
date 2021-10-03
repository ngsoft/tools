<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use NGSOFT\Traits\ContainerAware;
use Psr\EventDispatcher\{
    EventDispatcherInterface, ListenerProviderInterface, StoppableEventInterface
};

/**
 * Event Dispatcher that forwards call to another dispatcher,
 * and dispatches event to a set listener
 * or just returns the event (Null Dispatcher)
 */
final class EventDispatcher implements EventDispatcherInterface {

    use EventDispatcherAware,
        ContainerAware;

    /** @var ?ListenerProviderInterface */
    private $eventListener;

    /**
     * Support for autowiring
     * @param ?ListenerProviderInterface $eventListener Event Listener to listen to
     */
    public function __construct(
            ListenerProviderInterface $eventListener = null
    ) {
        $this->eventListener = $eventListener;
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
     * @param ListenerProviderInterface $eventListener
     */
    public function setEventListener(ListenerProviderInterface $eventListener) {
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
                $this->getEventListener() instanceof ListenerProviderInterface and
                !$this->isStopped($event)
        ) {

            foreach ($this->getEventListener()->getListenersForEvent($event) as $call) {
                if ($this->isStopped($event)) break;
                $call($event);
            }
        }

        if (
                $this->getEventDispatcher() instanceof EventDispatcherInterface and
                !$this->isStopped($event)
        ) {
            $event = $this->getEventDispatcher()->dispatch($event);
        }

        return $event;
    }

    /**
     * @param object $event
     * @return bool
     */
    private function isStopped(object $event): bool {
        return
                $event instanceof StoppableEventInterface and
                $event->isPropagationStopped();
    }

}
