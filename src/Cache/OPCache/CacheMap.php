<?php

namespace NGSOFT\Tools\Cache\OPCache;

use ArrayObject;
use NGSOFT\Tools\Interfaces\CacheAble;

/**
 * Contains the reference of all the keys that are being managed
 */
class CacheMap extends ArrayObject implements CacheAble {

    private $pool;

    /**
     * Add an entry to the stack
     * @param string $key
     * @param array $data
     */
    public function addEntry(string $key, array $data) {
        $this->offsetSet($key, $data);
    }

    /**
     * Get an entry from the stack
     * @param string $key
     * @return type
     */
    public function getEntry(string $key) {
        return $this->offsetExists($key) ? $this->offsetGet($key) : null;
    }

    /**
     * Remove an entry from the stack
     * @param string $key
     */
    public function removeEntry(string $key) {
        $this->offsetUnset($key);
    }

    /**
     * @param BasicCachePool $pool
     */
    public function setPool(BasicCachePool $pool) {
        $this->pool = $pool;
    }

    ////////////////////////////   CacheAble   ////////////////////////////


    public function toArray(): array {
        return $this->getArrayCopy();
    }

    public static function __set_state(array $data): self {
        return new static($data);
    }

}
