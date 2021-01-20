<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use Psr\EventDispatcher\{
    EventDispatcherInterface as PSREventDispatcherInterface, ListenerProviderInterface, StoppableEventInterface
};
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

/**
 * Event Dispatcher that forwards call to another dispatcher,
 * or dispatches event to a set listener
 * or just returns the event (Null Dispatcher)
 */
final class EventDispatcher implements SymfonyEventDispatcherInterface {

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
     * @param object      $event     The event to pass to the event handlers/listeners
     * @param string|null $eventName The name of the event to dispatch. If not supplied,
     *                               the class of $event should be used instead. if the set EventDispatcher is not Symfony one it will not be used
     *
     * @return object The passed $event MUST be returned
     */
    public function dispatch(object $event, string $eventName = null): object {
        if (
                ($stoppable = $event instanceof StoppableEventInterface) and
                $event->isPropagationStopped()
        ) {
            return $event;
        }


        if ($this->eventDispatcher instanceof SymfonyEventDispatcherInterface) {
            return $this->eventDispatcher->dispatch($event, $eventName);
        } elseif ($this->eventDispatcher instanceof PSREventDispatcherInterface) {
            return $this->eventDispatcher->dispatch($event);
        }

        $eventName = $eventName ?? get_class($event);
        if ($this->eventListener instanceof ListenerProviderInterface) {

            foreach ($this->eventListener->getListenersForEvent($event) as $call) {
                if ($stoppable and $event->isPropagationStopped()) break;
                //keep compatibility with symfony
                $call($event, $eventName, $this);
            }
        }

        return $event;
    }

}
