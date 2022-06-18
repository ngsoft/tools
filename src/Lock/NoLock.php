<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

class NoLock extends BaseLockStore
{

    /**
     * {@inheritdoc}
     * @phan-suppress PhanUnusedProtectedMethodParameter
     */
    protected function isOwner(string $currentOwner): bool
    {
        return true;
    }

    public function acquire(): bool
    {
        return true;
    }

    public function forceRelease(): void
    {

    }

    public function getRemainingLifetime(): float|int
    {
        return 0;
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
