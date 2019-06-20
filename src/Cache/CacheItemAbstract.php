<?php

namespace NGSOFT\Tools\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Fig\Cache\BasicCacheItemAccessorsTrait;
use NGSOFT\Tools\Traits\LoggerAwareWriter;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerAwareTrait;

class CacheItemAbstract implements CacheItemInterface {

    use BasicCacheItemAccessorsTrait;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var boolean
     */
    protected $hit;

    /**
     * @var DateTime
     */
    protected $expiration;

    /**
     * Constructs a new CacheItem.
     *
     * @param string $key
     *   The key of the cache item this object represents.
     * @param array $data
     *   An associative array of data from the Memory Pool.
     */
    public function __construct(\Psr\Cache\CacheItemPoolInterface $key, array $data) {
        $this->key = $key;
        $this->value = $data['value'];
        $this->expiration = $data['ttd'];
        $this->hit = $data['hit'];
    }

    /**
     * {@inheritdoc}
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function get() {
        return $this->isHit() ? $this->value : null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value = null) {
        $this->value = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit() {
        return $this->hit;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration) {
        if (is_null($expiration)) {
            $this->expiration = new DateTime('now +1 year');
        } else {
            assert($expiration instanceof DateTimeInterface);
            $this->expiration = $expiration;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time) {
        if (is_null($time)) {
            $this->expiration = new DateTime('now +1 year');
        } elseif (is_numeric($time)) {
            $this->expiration = new DateTime('now +' . $time . ' seconds');
        } else {
            assert($time instanceof DateInterval);
            $expiration = new DateTime();
            $expiration->add($time);
            $this->expiration = $expiration;
        }
        return $this;
    }

    /**
     * Returns the expiration timestamp.
     *
     * Although not part of the CacheItemInterface, this method is used by
     * the pool for extracting information for saving.
     *
     * @return \DateTime
     *   The timestamp at which this cache item should expire.
     *
     * @internal
     */
    public function getExpiration() {
        return $this->expiration ?: new \DateTime('now +1 year');
    }

    /**
     * Returns the raw value, regardless of hit status.
     *
     * Although not part of the CacheItemInterface, this method is used by
     * the pool for extracting information for saving.
     *
     * @return mixed
     *
     * @internal
     */
    public function getRawValue() {
        return $this->value;
    }

}
