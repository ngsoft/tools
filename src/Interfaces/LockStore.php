<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface LockStore
{

    /**
     * Acquires the lock.
     * @return bool
     */
    public function acquire(): bool;

    /**
     * Returns whether or not the lock is acquired.
     * @return bool
     */
    public function isAcquired(): bool;

    /**
     * Returns the remaining lifetime in seconds.
     *
     * @return float|int
     */
    public function getRemainingLifetime(): float|int;

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
    public function getOwner(): string;

    /**
     * Releases this lock in disregard of ownership.
     *
     * @return void
     */
    public function forceRelease(): void;
}
