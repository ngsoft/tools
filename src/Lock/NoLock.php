<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

/**
 * NullLock
 */
class NoLock extends BaseLockStore
{

    /** {@inheritdoc} */
    public function acquire(): bool
    {
        return true;
    }

    protected function read(): array|false
    {
        return false;
    }

    protected function write(): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function forceRelease(): void
    {

    }

    public function isAcquired(): bool
    {
        return true;
    }

    public function release(): bool
    {
        return true;
    }

}
