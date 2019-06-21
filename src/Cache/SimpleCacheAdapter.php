<?php

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Exceptions\PSRCacheInvalidKey;
use NGSOFT\Tools\Objects\Collection;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Throwable;

/**
 * PSR6 to PSR16 Simple Cache Adapter
 */
class SimpleCacheAdapter implements CacheInterface {

    ////////////////////////////   Inject PSR6   ////////////////////////////

    private $pool;

    /**
     * @return CacheItemPoolInterface
     */
    public function getPool(): CacheItemPoolInterface {
        return $this->pool;
    }

    public function __construct(CacheItemPoolInterface $pool, int $ttl = null) {
        $this->pool = $pool;
        if (isset($ttl)) $this->setTTL($ttl);
    }

    ////////////////////////////   TTL   ////////////////////////////

    /** @var int */
    protected $ttl = 60;

    /**
     * @return int
     */
    public function getTTL(): int {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     */
    public function setTTL(int $ttl) {
        $this->ttl = $ttl;
    }

    ////////////////////////////   SimpleCache   ////////////////////////////

    /**
     * {@inheritdoc}
     */
    public function clear() {
        return $this->pool->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key) {
        try {
            return $this->pool->deleteItem($key);
        } catch (Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys) {
        try {
            return $this->pool->deleteItems($keys);
        } catch (Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null) {
        try {
            $item = $this->pool->getItem($key);
            if (!$item->isHit()) return $default;
            return $item->get();
        } catch (Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null) {
        try {
            return (new Collection($this->pool->getItems($keys)))
                            ->map(function ($item) use($default) {
                                if (!$item->isHit()) return $default;
                                return $item->get();
                            })
                            ->toArray();
        } catch (Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {
        try {
            return $this->pool->hasItem($key);
        } catch (Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null) {
        try {
            $item = $this->pool
                    ->getItem($key)
                    ->set($value)
                    ->expiresAfter($ttl ?? $this->ttl);
            return $this->pool->save($item);
        } catch (Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null) {
        $success = true;
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) $success = false;
        }
        return $success;
    }

}
