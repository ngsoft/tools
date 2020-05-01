<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Cache\Exceptions\SimpleCache\InvalidArgument;
use Psr\{
    Cache\CacheItemPoolInterface, Cache\InvalidArgumentException, SimpleCache\CacheInterface
};

/**
 * PSR6 to PSR16 Simple Cache Adapter
 */
final class SimpleCacheAdapter implements CacheInterface {
    ////////////////////////////   Inject PSR6   ////////////////////////////

    /** @var CacheItemPoolInterface */
    private $pool;

    /** @return CacheItemPoolInterface */
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
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgument($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys) {
        try {
            return $this->pool->deleteItems((array) $keys);
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgument($ex->getMessage(), $ex->getCode());
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
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgument($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null) {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {
        try {
            return $this->pool->hasItem($key);
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgument($ex->getMessage(), $ex->getCode());
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
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgument($ex->getMessage(), $ex->getCode());
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
