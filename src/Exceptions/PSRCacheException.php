<?php

namespace NGSOFT\Tools\Exceptions;

use Psr\Cache\CacheException;
use Psr\SimpleCache\CacheException as SimpleCacheException;
use RuntimeException;

class PSRCacheException extends RuntimeException implements SimpleCacheException, CacheException {

}
