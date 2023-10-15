<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

trait StoppableEventTrait
{
    protected bool $propagationStopped = false;

    /**
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     *
     * @return bool
     *              True if the Event is complete and no further listeners should be called.
     *              False to continue calling listeners.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stop propagation for event.
     *
     * @phan-suppress PhanTypeMismatchReturn
     */
    public function stopPropagation(): static
    {
        $this->propagationStopped = true;
        return $this;
    }
}
