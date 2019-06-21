<?php

namespace NGSOFT\Tools\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class SimpleCacheAdapter implements CacheInterface {

    private $pool;

    public function __construct(CacheItemPoolInterface $pool) {
        $this->pool = $pool;
    }

    public function clear() {

    }

    public function delete($key) {

    }

    public function deleteMultiple($keys) {

    }

    public function get($key, $default = null) {

    }

    public function getMultiple($keys, $default = null) {

    }

    public function has($key) {

    }

    public function set($key, $value, $ttl = null) {

    }

    public function setMultiple($values, $ttl = null) {

    }

}
