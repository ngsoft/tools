<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface LockStore
{

    /**
     * Attempt to acquire the lock.
     *
     * @param  callable|null  $callback
     * @return mixed
     */
    public function get(callable $callback = null): mixed;

    /**
     * Attempt to acquire the lock for the given number of seconds.
     *
     * @param  int  $seconds
     * @param  callable|null  $callback
     * @return mixed
     */
    public function block($seconds, $callback = null): mixed;

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
