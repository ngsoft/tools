<?php

use Fig\Cache\BasicPoolTrait;
use Fig\Cache\KeyValidatorTrait;
use Psr\Cache\CacheItemPoolInterface;

namespace NGSOFT\Tools\Cache;

class OPCachePool implements CacheItemPoolInterface {

    use BasicPoolTrait;
    use KeyValidatorTrait;
}
