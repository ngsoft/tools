<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * A Base Event to extends
 */
abstract class Event implements StoppableEventInterface {

    private $propagationStopped = false;

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped(): bool {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     */
    public function stopPropagation(): void {
        $this->propagationStopped = true;
    }

}
