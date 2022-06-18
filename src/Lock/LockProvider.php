<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

interface LockProvider
{

    /**
     * Get a LockStore instance
     *
     * @param string $name Lock name
     * @param int|float $seconds maximum lock duration
     * @param ?string $owner owner, for shared locks
     * @return LockStore
     */
    public function lock(string $name, int|float $seconds = 0, string $owner = null): LockStore;

    /**
     * Restore a lock using the owner id
     *
     * @param string $name
     * @param string $owner
     * @return LockStore
     */
    public function restoreLock(string $name, string $owner): LockStore;
}
