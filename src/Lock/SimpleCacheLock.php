<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use Psr\SimpleCache\CacheInterface,
    RuntimeException;

if ( ! interface_exists(CacheInterface::class)) {
    throw new RuntimeException('psr/simple-cache not installed, please install a PSR-16 cache');
}

/**
 * Use SimpleCache to manage your locks
 */
class SimpleCacheLock extends CacheLockAbstract
{

    public function __construct(
            protected CacheInterface $cache,
            string $name,
            int|float $seconds = 0,
            string $owner = '',
            bool $autoRelease = true
    )
    {
        parent::__construct($name, $seconds, $owner, $autoRelease);
    }

    /** {@inheritdoc} */
    protected function read(): array|false
    {
        $result = $this->cache->get($this->getCacheKey());
        return is_array($result) ? $result : false;
    }

    protected function write(int|float $until): bool
    {

        return $this->cache->set(
                        $this->getCacheKey(),
                        $this->createEntry($until),
                        (int) ceil($until - $this->timestamp())
        );
    }

    /** {@inheritdoc} */
    public function forceRelease(): void
    {
        if ($this->cache->delete($this->getCacheKey())) {
            $this->until = 1;
        }
    }

}
