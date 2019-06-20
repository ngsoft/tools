<?php

namespace NGSOFT\Tools\Cache;

class CacheManager {

    private $pool;
    private $item;

    /**
     * @param CachePoolAbstract $pool
     * @param CacheItemAbstract $item
     */
    public function __construct(CachePoolAbstract $pool, CacheItemAbstract $item) {
        $this->pool = $pool;
        $this->item = $item;
    }

    public function getPool(): CachePoolAbstract {
        return $this->pool;
    }

    public function getItem(): CacheItemAbstract {
        return $this->item;
    }

}
