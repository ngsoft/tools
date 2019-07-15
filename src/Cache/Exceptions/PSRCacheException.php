<?php

namespace NGSOFT\Tools\Cache\Exceptions;

use NGSOFT\Tools\Exceptions\RuntimeException;
use Psr\{
    Cache\CacheException, SimpleCache\CacheException as SimpleCacheException
};

class PSRCacheException extends RuntimeException implements CacheException, SimpleCacheException {

}
