<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Use a cache pool to manage your locks
 */
class CacheLock extends CacheLockAbstract
{

    public function __construct(
            protected CacheItemPoolInterface $cache,
            string $name,
            int|float $seconds = 0,
            string $owner = '',
            bool $autoRelease = true
    )
    {
        parent::__construct($name, $seconds, $owner, $autoRelease);
    }

    protected function read(): array|false
    {

        $item = $this->cache->getItem($this->getCacheKey());
        if ($item->isHit() && is_array($item->get())) {
            return $item->get();
        }

        return false;
    }

    protected function write(int|float $until): bool
    {
        return $this->cache->save(
                        $this->cache
                                ->getItem($this->getCacheKey())
                                ->set($this->createEntry($until))
                                ->expiresAt(date_timestamp_set(
                                                date_create(),
                                                (int) ceil($until))
                                )
        );
    }

    /** {@inheritdoc} */
    public function forceRelease(): void
    {
        if ($this->cache->deleteItem($this->getCacheKey())) {
            $this->until = 1;
        }
    }

}
