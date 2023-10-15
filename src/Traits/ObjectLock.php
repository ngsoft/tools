<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use RuntimeException;

/**
 * Adds Locking capability.
 */
trait ObjectLock
{
    protected bool $locked = false;

    /**
     * Lock the object.
     */
    public function lock(): void
    {
        $this->locked = true;
    }

    /**
     * Unlock the object.
     */
    public function unlock(): void
    {
        $this->locked = false;
    }

    /**
     * Get the lock status.
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Throws a RuntimeException if locked.
     */
    protected function assertLocked(): void
    {
        if ($this->isLocked())
        {
            throw new \RuntimeException(sprintf('%s is locked.', static::class));
        }
    }
}
