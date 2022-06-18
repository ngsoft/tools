<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

abstract class CacheLockAbstract extends BaseLockStore
{

    protected const CACHE_KEY_MODIFIER = 'CACHELOCK[%s]';

    protected function getCacheKey(): string
    {
        // prevents filenames to throw cache errors
        return sprintf(self::CACHE_KEY_MODIFIER, $this->getHashedName());
    }

    protected function createEntry(int|float $until): array
    {

        $data = [
            static::KEY_UNTIL => $until,
            static::KEY_OWNER => $this->getOwner(),
        ];

        return $data;
    }

}
