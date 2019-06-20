<?php

namespace NGSOFT\Tools\Cache;

use DateTime;
use Fig\Cache\BasicPoolTrait;
use Fig\Cache\KeyValidatorTrait;
use Fig\Cache\Memory\MemoryCacheItem;
use NGSOFT\Tools\Traits\LoggerAwareWriter;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\SimpleCache\CacheInterface;

class OPCachePool extends CachePoolAbstract {

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
     * {@inheritdoc}
     */
    public function getItem($key) {
        // This method will either return True or throw an appropriate exception.
        $this->validateKey($key);

        if (!$this->hasItem($key)) {
            $this->data[$key] = $this->emptyItem();
        }

        return new MemoryCacheItem($key, $this->data[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear() {
        $this->data = [];
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key) {
        return array_key_exists($key, $this->data) && $this->data[$key]['ttd'] > new DateTime();
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $items) {
        /** @var CacheItemInterface $item  */
        foreach ($items as $item) {
            $this->data[$item->getKey()] = [
                // Assumes use of the BasicCacheItemAccessorsTrait.
                'value' => $item->getRawValue(),
                'ttd' => $item->getExpiration(),
                'hit' => true,
            ];
        }

        return true;
    }

}
