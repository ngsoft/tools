<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

class BasicCacheItem implements CacheItemInterface {

    /**
     * @var string
     */
    private $key;

    /**
     * @var bool
     */
    private $hit;

    /**
     * @var DateTime
     */
    private $expire;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $key
     * @param bool $hit
     * @param int $ttl
     * @param mixed $value
     */
    public function __construct(string $key, bool $hit, int $expire, $value) {
        $this->key = $key;
        $this->hit = $hit;
        $this->value = $value;
        $this->expiresAt($expire);
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time) {

        if (is_numeric($time)) $this->expiresAt(DateTime(sprintf("now +%d seconds", (int) $time)));
        else {
            assert($time instanceof DateInterval);
            $this->expiresAt((new \DateTime())->add($time));
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expire) {
        if ($expire === null) $this->meta->setExpire($this->meta->getPool()->getTTL() + time());
        else {
            assert($expire instanceof DateTimeInterface);
            $this->expire = $expire;
            $this->meta->setExpire($expire->getTimestamp());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get() {
        return $this->value;
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
    public function isHit() {
        return $this->hit;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value) {
        $this->value = $value;
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
        return $this->expire;
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
