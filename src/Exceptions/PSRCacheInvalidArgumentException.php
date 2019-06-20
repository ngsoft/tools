<?php

namespace NGSOFT\Tools\Exceptions;

use Fig\Cache\InvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentException;

class PSRCacheInvalidArgumentException extends InvalidArgumentException implements SimpleCacheInvalidArgumentException {

}
