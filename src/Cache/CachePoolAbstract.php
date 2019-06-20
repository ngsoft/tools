<?php

namespace NGSOFT\Tools\Cache;

use Fig\Cache\BasicPoolTrait;
use Fig\Cache\KeyValidatorTrait;
use NGSOFT\Tools\Traits\LoggerAwareWriter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\SimpleCache\CacheInterface;

class CachePoolAbstract implements CacheItemPoolInterface, LoggerAwareInterface, CacheInterface {

    use BasicPoolTrait;
    use KeyValidatorTrait;
    use LoggerAwareTrait;
    use LoggerAwareWriter;

    /**
     * Items already loaded from cache
     * @var array<string,CacheItemInterface>
     */
    protected $cached = [];

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

    public function getTTL(): int {
        return $this->ttl;
    }

    ////////////////////////////   CacheItemPool   ////////////////////////////

    /**
     * Returns an empty item definition.
     *
     * @return array
     */
    protected function emptyItem() {
        return [
            'value' => null,
            'hit' => false,
            'ttd' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = []) {
        // This method will throw an appropriate exception if any key is not valid.
        array_map([$this, 'validateKey'], $keys);

        $collection = [];
        foreach ($keys as $key) {
            $collection[$key] = $this->getItem($key);
        }
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys) {
        foreach ($keys as $key) {
            unset($this->data[$key]);
        }
    }

    ////////////////////////////   SimpleCache   ////////////////////////////

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
    public function has($key): bool {

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
