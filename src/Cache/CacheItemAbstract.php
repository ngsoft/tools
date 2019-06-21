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
        if ($time === null) $time = $this->meta->getPool()->getTTL();
        if (is_numeric($time)) $this->meta->setExpire(new \DateTime(sprintf('now +%d seconds', (int) $time)));
        else {
            assert($time instanceof \DateInterval);
            $this->meta->setExpire((new \DateTime())->add($time));
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expire) {
        if ($expire === null) {
            $this->meta->setExpire(new \DateTime(sprintf("now +%d seconds", $this->meta->getPool()->getTTL())));
        } else {
            assert($expire instanceof \DateTimeInterface);
            $this->meta->setExpire($expire);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get() {
        return $this->meta->getValue();
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
