<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface LockStore
{

    /**
     * Acquires the lock. If the lock is acquired by someone else, the parameter `blocking` determines whether or not
     * the call should block until the release of the lock.
     *
     * @throws LockConflictedException If the lock is acquired by someone else in blocking mode
     * @throws LockAcquiringException  If the lock cannot be acquired
     */
    public function acquire(bool $blocking = false): bool;

    /**
     * Returns whether or not the lock is acquired.
     */
    public function isAcquired(): bool;

    /**
     * Returns the remaining lifetime in seconds.
     */
    public function getRemainingLifetime(): float|int|null;

    /**
     * Attempt to acquire the lock.
     *
     * @param  callable|null  $callback
     * @return mixed
     */
    public function get(?callable $callback = null): mixed;

    /**
     * Attempt to acquire the lock for the given number of seconds.
     *
     * @param  int|float  $seconds
     * @param  callable|null  $callback
     * @return mixed
     */
    public function block(int|float $seconds, ?callable $callback = null): mixed;

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release(): bool;

    /**
     * Returns the current owner of the lock.
     *
     * @return string
     */
    public function owner(): string;

    /**
     * Releases this lock in disregard of ownership.
     *
     * @return void
     */
    public function forceRelease(): void;
}
