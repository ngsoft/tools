<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use Psr\EventDispatcher\EventDispatcherInterface;

trait DispatcherAware
{

    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @return void
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        if ($eventDispatcher instanceof self) {
            return;
        }
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Dispaches event using the provided event dispatcher
     *
     * @param object $event
     * @return object
     */
    protected function dispatchEvent(object $event): object
    {
        return $this->eventDispatcher?->dispatch($event) ?? $event;
    }

}
