<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use DateTime;
use NGSOFT\Tools\Exceptions\BasicCacheInvalidKey;
use NGSOFT\Tools\Exceptions\PSRCacheInvalidKey;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Throwable;
use function NGSOFT\Tools\array_every;

/**
 * PSR6/PSR16 Compatible Cache Pool implementation
 */
abstract class BasicCachePool extends SimpleCacheAdapter implements CacheItemPoolInterface, LoggerAwareInterface {

    use LoggerAwareTrait;

    /** @var array */
    protected $deferred = [];

    ////////////////////////////   TTL   ////////////////////////////

    /** @var int */
    protected $ttl = 60;

    /**
     * Set the default ttl value for the cache
     * @param int $ttl
     * @return $this
     */
    public function setTTL(int $ttl) {
        $this->ttl = $ttl;
    }

    /**
     * Get Default TTL Value
     * @return int
     */
    public function getTTL(): int {
        return $this->ttl;
    }

    ////////////////////////////   ContainerAware   ////////////////////////////

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     * @param int|null $ttl
     */
    public function __construct(int $ttl = null) {
        if (isset($ttl)) $this->ttl = $ttl;
        //initialize PSR16
        parent::__construct($this, $ttl);
    }

    /**
     * Inject the Container
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) {
        if ($container->has(LoggerInterface::class)) {
            $this->setLogger($container->get(LoggerInterface::class));
        }
    }

    ////////////////////////////   Empty Item   ////////////////////////////

    /**
     * Creates a empty item
     * @param string $key
     * @return CacheMeta
     */
    protected function createEmptyItem(string $key): BasicCacheItem {
        $expire = new DateTime(sprintf('now +%d seconds', $this->ttl));
        return new BasicCacheItem($key, $expire, false, null);
    }

    ////////////////////////////   Abstract Methods   ////////////////////////////

    /**
     * Deletes all items in the pool.
     * @return bool
     */
    abstract protected function clearCache(): bool;

    /**
     * Loads an item from the cache
     * @param string $key
     * @return BasicCacheItem
     */
    abstract protected function readCache(string $key): BasicCacheItem;

    /**
     * Commits the specified cache item to storage.
     * @param BasicCacheItem $item
     * @return bool
     */
    abstract protected function writeCache(BasicCacheItem $item): bool;

    /**
     * Removes a single items from the pool.
     * @param string $key
     * @return bool
     */
    abstract protected function deleteCache(string $key): bool;

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     * @param string $key
     * @return bool
     */
    abstract protected function hasCache(string $key): bool;


    ////////////////////////////   LoggerInterface   ////////////////////////////

    /**
     * Checks if logger is defined and logs a debug message
     * @param string $message
     */
    public function log(string $message) {
        if (isset($this->logger)) $this->logger->debug($message);
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
     * @throws PSRCacheInvalidKey
     *   An exception implementing The Cache InvalidArgumentException interface
     *   will be thrown if the key does not validate.
     * @return bool
     *   TRUE if the specified key is legal.
     */
    protected function validateKey($key) {
        try {
            if (!is_string($key) || $key === '') {
                throw new BasicCacheInvalidKey('Key should be a non empty string');
            }
            $unsupportedMatched = preg_match('#[' . preg_quote($this->getReservedKeyCharacters()) . ']#', $key);
            if ($unsupportedMatched > 0) {
                throw new BasicCacheInvalidKey('Can\'t validate the specified key');
            }
            return true;
        } catch (Throwable $exc) {
            //log the message
            $this->log($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear() {
        return $this->clearCache();
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key) {
        return $this->hasCache($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys) {
        array_map([$this, 'validateKey'], $keys);
        return array_every([$this, 'deleteItem'], $keys);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key) {
        $this->validateKey($key);
        return $this->deleteCache($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key) {
        $this->validateKey($key);
        return $this->loadCache($key);
    }

    /**
     * {@inheritdoc}
     * @return array<string,BasicCacheItem>
     */
    public function getItems(array $keys = []) {
        array_map([$this, 'validateKey'], $keys); $list = [];
        foreach ($keys as $key) {
            $list[$key] = $this->getItem($key);
        }
        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item) {
        return $this->writeCache($item);
    }

    /**
     * {@inheritdoc}
     */
    public function commit() {
        $success = array_every([$this, "save"], $this->deferred);
        $this->deferred = [];
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item) {
        $this->deferred[] = $item;
    }

}
