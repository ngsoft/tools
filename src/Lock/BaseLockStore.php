<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

class BaseLockStore implements \NGSOFT\Interfaces\LockStore
{

    public function acquire(bool $blocking = false): bool
    {

    }

    public function block(int|float $seconds, mixed $callback = null): mixed
    {

    }

    public function forceRelease(): void
    {

    }

    public function get(mixed $callback = null): mixed
    {

    }

    public function getRemainingLifetime(): float|int
    {

    }

    public function isAcquired(): bool
    {

    }

    public function owner(): string
    {

    }

    public function release(): bool
    {

    }

}
