<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Fig\Cache\BasicCacheItemAccessorsTrait;
use NGSOFT\Tools\Traits\LoggerAwareWriter;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerAwareTrait;

class BasicCacheItem implements CacheItemInterface {

    /**
     * @var CacheMeta
     */
    private $meta;

    public function __construct(CacheMeta $meta) {
        $this->manager = $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time) {
        if ($time === null) $time = $this->meta->getPool()->getTTL() + time();

        if (is_numeric($time)) $this->meta->setExpire((int) $time);
        else {
            assert($time instanceof \DateInterval);
            $this->meta->setExpire((new \DateTime())->add($time)->getTimestamp());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expire) {
        if ($expire === null) $this->meta->setExpire($this->meta->getPool()->getTTL() + time());
        else {
            assert($expire instanceof \DateTimeInterface);
            $this->meta->setExpire($expire->getTimestamp());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get() {
        return $this->isHit() ? $this->meta->getValue() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey() {
        return $this->meta->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function isHit() {
        return $this->meta->getHit();
    }

    /**
     * {@inheritdoc}
     */
    public function set($value) {
        $this->meta->setValue($value);
    }

}
