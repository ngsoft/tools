<?php

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Exceptions\PSRCacheInvalidArgumentException;
use NGSOFT\Tools\Traits\LoggerWriter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SplObjectStorage;

class CachePoolAbstract implements CacheItemPoolInterface, LoggerAwareInterface {

    use LoggerAwareTrait;
    use LoggerWriter;

    /**
     * Items already loaded from cache
     * @var array<string,CacheManager>
     */
    protected $loaded = [];

    /** @var SplObjectStorage */
    protected $deferred;

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
     * Characters which cannot be used in cache key.
     *
     * The characters returned by this function are reserved for future extensions and MUST NOT be
     * supported by implementing libraries
     *
     * @return string
     */
    final public function getReservedKeyCharacters() {
        return '{}()/\@:';
    }

    /**
     * Determines if the specified key is legal under PSR-6.
     *
     * @param string $key
     *   The key to validate.
     * @throws PSRCacheInvalidArgumentException
     *   An exception implementing The Cache InvalidArgumentException interface
     *   will be thrown if the key does not validate.
     * @return bool
     *   TRUE if the specified key is legal.
     */
    protected function validateKey($key) {
        if (!is_string($key) || $key === '') {
            throw new PSRCacheInvalidArgumentException('Key should be a non empty string');
        }
        $unsupportedMatched = preg_match('#[' . preg_quote($this->getReservedKeyCharacters()) . ']#', $key);
        if ($unsupportedMatched > 0) {
            throw new PSRCacheInvalidArgumentException('Can\'t validate the specified key');
        }
        return true;
    }

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
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key) {

    }

    /**
     * {@inheritdoc}
     */
    public function getItems(string $keys = array()) {

    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key) {

    }

    /**
     * {@inheritdoc}
     */
    public function save($item) {

    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred($item) {

    }

}
