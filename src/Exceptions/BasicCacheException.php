<?php

namespace NGSOFT\Tools\Exceptions;

use Psr\Cache\CacheException as CacheException2;
use Psr\SimpleCache\CacheException;
use RuntimeException;

class BasicCacheException extends RuntimeException implements CacheException, CacheException2 {

}
