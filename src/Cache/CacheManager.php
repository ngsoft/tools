<?php

namespace NGSOFT\Tools\Cache;

class CacheManager {

    private $pool;
    private $item;

    /**
     * @param BasicCachePool $pool
     * @param CacheItemAbstract $item
     */
    public function __construct(BasicCachePool $pool, CacheItemAbstract $item) {
        $this->pool = $pool;
        $this->item = $item;
    }

    public function getPool(): BasicCachePool {
        return $this->pool;
    }

    public function getItem(): CacheItemAbstract {
        return $this->item;
    }

}
