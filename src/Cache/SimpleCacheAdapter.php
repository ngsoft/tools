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

    }

    /**
     * {@inheritdoc}
     */
    public function delete($key) {

    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys) {

    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null) {

    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null) {

    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {

    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null) {

    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null) {

    }

}
