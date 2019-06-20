<?php

use Fig\Cache\BasicPoolTrait;
use Fig\Cache\KeyValidatorTrait;
use Psr\Cache\CacheItemPoolInterface;

namespace NGSOFT\Tools\Cache;

class OPCachePool implements CacheItemPoolInterface {

    use BasicPoolTrait;
    use KeyValidatorTrait;

    /**
     * The stored data in this cache pool.
     *
     * @var array
     */
    protected $data = [];

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
    public function clear() {
        $this->data = [];
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys) {
        foreach ($keys as $key) {
            unset($this->data[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key) {
        return array_key_exists($key, $this->data) && $this->data[$key]['ttd'] > new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $items) {
        /** @var \Psr\Cache\CacheItemInterface $item  */
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
