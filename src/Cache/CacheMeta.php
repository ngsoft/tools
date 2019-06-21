<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use DateTime;

class CacheMeta {

    /** @var BasicCachePool */
    private $pool;

    /** @var CacheItemAbstract */
    private $item;

    /** @var bool */
    private $hit;

    /** @var DateTime */
    private $expire;

    /** @var string */
    private $key;

    /** @var string */
    private $internalKey;

    /** @var mixed */
    private $value;

    ////////////////////////////   Getter/Setter   ////////////////////////////

    /**
     * @return bool
     */
    public function getHit(): bool {
        return $this->hit === true;
    }

    /** @return DateTime */
    public function getExpire(): DateTime {
        return $this->expire;
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
        return $this->value;
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
     * @param DateTime $expire
     * @return static
     */
    public function setExpire(DateTime $expire): self {
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
     * @param CacheItemAbstract $item
     */
    public function __construct(BasicCachePool $pool, CacheItemAbstract $item) {
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
     * @return CacheItemAbstract
     */
    public function getItem(): CacheItemAbstract {
        return $this->item;
    }

}
