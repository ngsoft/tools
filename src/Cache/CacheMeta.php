<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use DateTime;

class CacheMeta {

    /** @var BasicCachePool */
    private $pool;

    /** @var BasicCacheItem */
    private $item;

    /** @var bool */
    private $hit = false;

    /** @var int */
    private $expire;

    /** @var string */
    private $key;

    /** @var string */
    private $internalKey;

    /** @var mixed */
    private $value;

    ////////////////////////////   Getter/Setter   ////////////////////////////

    /** @return int */
    public function getTTL(): int {
        return $this->pool->getTTL();
    }

    /** @return bool */
    public function getHit(): bool {
        return $this->hit === true;
    }

    /** @return DateTime */
    public function getExpire(): DateTime {

        return new DateTime(date(\DateTime::ISO8601, $this->expire));
    }

    /** @return string */
    public function getKey(): string {
        return $this->key;
    }

    /** @return string */
    public function getInternalKey(): string {
        return $this->internalKey;
    }

    /** @return mixed */
    public function getValue() {
        return $this->value = $this->value ?? $this->pool->getCache($this->item);
    }

    /**
     * @param bool $hit
     * @return static
     */
    public function setHit(bool $hit): self {
        $this->hit = $hit;
        return $this;
    }

    /**
     * @param int $expire
     * @return static
     */
    public function setExpire(int $expire): self {
        $this->expire = $expire;
        return $this;
    }

    /**
     * @param string $key
     * @return static
     */
    public function setKey(string $key): self {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $internalKey
     * @return static
     */
    public function setInternalKey(string $internalKey): self {
        $this->internalKey = $internalKey;
        return $this;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setValue(string $value): self {
        $this->value = $value;
        return $this;
    }

    ////////////////////////////   Wiring   ////////////////////////////

    /**
     * @param BasicCachePool $pool
     * @param BasicCacheItem $item
     */
    public function __construct(BasicCachePool $pool, BasicCacheItem $item) {
        $this->pool = $pool;
        $this->item = $item;
    }

    /**
     * @return BasicCachePool
     */
    public function getPool(): BasicCachePool {
        return $this->pool;
    }

    /**
     * @return BasicCacheItem
     */
    public function getItem(): BasicCacheItem {
        return $this->item;
    }

}
