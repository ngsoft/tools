<?php

namespace NGSOFT\Tools\Cache\OPCache;

use NGSOFT\Tools\Cache\BasicCachePool;
use Psr\Cache\CacheItemInterface;
use Psr\Container\ContainerInterface;

/**
 * Uses PHP OPCode to store data
 */
class OPCachePool extends BasicCachePool {

    /** @var string */
    protected $path;

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    /**
     * @param string $path The directory where to store the cache
     * @param int $ttl Default TTL for a cache item
     * @param ContainerInterface $container
     */
    public function __construct(string $path, int $ttl = null, ContainerInterface $container = null) {
        parent::__construct($container, $ttl);
    }

    protected function clearCache(): bool {

    }

    protected function createCache(string $key): CacheItemInterface {

    }

    protected function deleteCache($keys): bool {

    }

    public function getCache(CacheItemInterface $item) {

    }

    protected function hasCache(string $key): bool {

    }

    protected function writeCache($items): bool {

    }

}
