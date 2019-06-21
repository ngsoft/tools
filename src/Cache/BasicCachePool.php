<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Exceptions\PSRCacheInvalidKey;
use NGSOFT\Tools\Objects\Collection;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use SplObjectStorage;
use Throwable;

/**
 * PSR6/PSR16 Compatible Cache Pool implementation
 */
abstract class BasicCachePool extends SimpleCacheAdapter implements CacheItemPoolInterface, LoggerAwareInterface {

    use LoggerAwareTrait;

    /** @var Collection */
    protected $loaded;

    /** @var SplObjectStorage */
    protected $deferred;

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
    public function __construct(ContainerInterface $container = null, int $ttl = null) {
        if (isset($container)) $this->setContainer($container);
        $this->deferred = new \SplObjectStorage();
        $this->loaded = new Collection();
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

    ////////////////////////////   Abstract Methods   ////////////////////////////

    /**
     * Creates an empty cache item
     * @param string $key
     * @return CacheItemInterface
     */
    abstract protected function createCache(string $key): CacheItemInterface;

    /**
     * Deletes all items in the pool.
     * @return bool
     */
    abstract protected function clearCache(): bool;

    /**
     * Commits the specified cache items to storage.
     * @param array<CacheItemInterface> $items
     * @return bool
     */
    abstract protected function writeCache(array $items): bool;

    /**
     * Removes multiple items from the pool.
     * @param array<string> $keys
     * @return bool
     */
    abstract protected function deleteCache(array $keys): bool;

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
                throw new PSRCacheInvalidKey('Key should be a non empty string');
            }
            $unsupportedMatched = preg_match('#[' . preg_quote($this->getReservedKeyCharacters()) . ']#', $key);
            if ($unsupportedMatched > 0) {
                throw new PSRCacheInvalidKey('Can\'t validate the specified key');
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
        // This method will either return True or throw an appropriate exception.
        array_map([$this, 'validateKey'], $keys);
        return $this->deleteCache($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key) {
        // This method will either return True or throw an appropriate exception.
        $this->validateKey($key);
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key) {
        // This method will either return True or throw an appropriate exception.
        $this->validateKey($key);
        return $this->loaded->get($key) ?? $this->createCache($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = []) {
        // This method will throw an appropriate exception if any key is not valid.
        array_map([$this, 'validateKey'], $keys); $list = [];
        foreach ($keys as $key) {
            $list[] = $this->getItem($key);
        }
        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item) {
        return $this->writeCache([$item]);
    }

    /**
     * {@inheritdoc}
     */
    public function commit() {
        $items = [];
        foreach ($this->loaded as $item) {
            if ($this->deferred->contains($item)) $items[] = $item;
        }
        if ($success = $this->writeCache($items)) $this->deferred = new \SplObjectStorage();
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item) {
        $this->deferred->attach($item);
    }

}
