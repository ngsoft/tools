<?php

namespace NGSOFT\Tools\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class SimpleCacheAdapter implements CacheInterface {

    private $pool;

    public function __construct(CacheItemPoolInterface $pool) {
        $this->pool = $pool;
    }

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

        } catch (\Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys) {
        try {

        } catch (\Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null) {
        try {

        } catch (\Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null) {
        try {

        } catch (\Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {
        try {

        } catch (\Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null) {
        try {

        } catch (\Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null) {
        try {

        } catch (\Throwable $ex) {
            throw new PSRCacheInvalidKey($ex->getMessage());
        }
    }

}
