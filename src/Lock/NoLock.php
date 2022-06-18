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

    /**
     * @phan-suppress PhanUnusedProtectedMethodParameter
     * @param int|float $until
     * @return bool
     */
    protected function write(int|float $until): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function forceRelease(): void
    {

    }

    /** {@inheritdoc} */
    public function isAcquired(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function release(): bool
    {
        return true;
    }

}
