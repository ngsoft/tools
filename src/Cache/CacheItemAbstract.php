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
     * @var CacheManager
     */
    private $manager;

    public function __construct(CacheManager $manager) {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time) {

    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration) {

    }

    /**
     * {@inheritdoc}
     */
    public function get() {

    }

    /**
     * {@inheritdoc}
     */
    public function getKey() {

    }

    /**
     * {@inheritdoc}
     */
    public function isHit() {

    }

    /**
     * {@inheritdoc}
     */
    public function set($value) {

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
        //return $this->expiration ?: new \DateTime('now +1 year');
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

    }

}
