<?php

namespace NGSOFT\Tools\Cache;

use Fig\Cache\BasicPoolTrait;
use Fig\Cache\KeyValidatorTrait;
use NGSOFT\Tools\Traits\LoggerAwareWriter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class CachePoolAbstract implements CacheItemPoolInterface, LoggerAwareInterface {

    use LoggerAwareTrait;
    use LoggerAwareWriter;

    /**
     * Items already loaded from cache
     * @var array<string,CacheManager>
     */
    protected $loaded = [];
    protected $deferred = [];

    /** @var int */
    protected $ttl = 60;

    /**
     * Set the default ttl value for the cache
     * @param int $ttl
     * @return $this
     */
    public function setTTL(int $ttl) {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Get Default TTL Value
     * @return int
     */
    public function getTTL(): int {
        return $this->ttl;
    }

    ////////////////////////////   CacheItemPool   ////////////////////////////

    /**
     * {@inheritdoc}
     */
    public function clear() {

    }

    /**
     * {@inheritdoc}
     */
    public function commit() {

    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key) {

    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(string $keys) {

    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key): \Psr\Cache\CacheItemInterface {

    }

    /**
     * {@inheritdoc}
     */
    public function getItems(string $keys = array()) {

    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key): bool {

    }

    /**
     * {@inheritdoc}
     */
    public function save(\Psr\Cache\CacheItemInterface $item) {

    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(\Psr\Cache\CacheItemInterface $item) {

    }

}
