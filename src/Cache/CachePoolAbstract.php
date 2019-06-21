<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Exceptions\PSRCacheInvalidArgumentException;
use NGSOFT\Tools\Objects\Collection;
use NGSOFT\Tools\Traits\ContainerAware;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use SplObjectStorage;

class CachePoolAbstract implements CacheItemPoolInterface, LoggerAwareInterface {

    use LoggerAwareTrait;
    use ContainerAware;

    /** @var Collection */
    protected $loaded;

    /** @var SplObjectStorage */
    protected $deferred;

    /** @var int */
    protected $ttl = 60;

    ////////////////////////////   ContainerAware   ////////////////////////////

    /**
     * @param ContainerInterface|null $container
     * @param int|null $ttl
     */
    public function __construct(ContainerInterface $container = null, int $ttl = null) {
        if (isset($container)) $this->setContainer($container);
        if (isset($ttl)) $this->setTTL($ttl);
        $this->deferred = new \SplObjectStorage();
        $this->loaded = new Collection();
        if ($this->has(LoggerInterface::class)) $this->setLogger($this->get(LoggerInterface::class));
    }

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
    public function save(CacheItemInterface $item) {

        return $this->write([$item]);
    }

    /**
     * {@inheritdoc}
     */
    public function commit() {
        $items = [];
        foreach ($this->loaded as $item) {
            if ($this->deferred->contains($item)) $items[] = $item;
        }
        if ($success = $this->write($items)) $this->deferred = new \SplObjectStorage();
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item) {
        $this->deferred->attach($item);
    }

}
