<?php

namespace NGSOFT\Tools\Exceptions;

use Fig\Cache\CacheException;
use Psr\SimpleCache\CacheException as SimpleCacheException;

class PSRCacheException extends CacheException implements SimpleCacheException {

}
