<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

final class CacheItem implements CacheItemInterface {

    /**  @var string */
    private $key;

    /** @var bool */
    private $hit;

    /** @var DateTime */
    private $expire;

    /** @var mixed */
    private $value;

    /** @var int */
    private $ttl;

    /**
     * @param string $key
     * @param int $ttl
     * @param bool $hit
     * @param mixed $value
     */
    public function __construct(string $key, int $ttl, bool $hit, $value) {
        $this->key = $key;
        $this->hit = $hit;
        $this->value = $value;
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time) {
        if (is_numeric($time)) $this->expiresAt(new DateTime(sprintf("now +%d seconds", (int) $time)));
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
        if ($expire === null) return $this->expiresAfter($this->ttl);
        else {
            assert($expire instanceof DateTimeInterface);
            $this->expire = $expire;
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
    public function getExpireAt() {
        if ($this->expire instanceof DateTime === false) $this->expiresAfter($this->ttl);
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
