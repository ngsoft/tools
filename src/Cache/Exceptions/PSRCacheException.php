<?php

namespace NGSOFT\Tools\Cache\Exceptions;

use Psr\{
    Cache\CacheException, Log\LoggerInterface, SimpleCache\CacheException as SimpleCacheException
};
use RuntimeException;

class PSRCacheException extends RuntimeException implements CacheException, SimpleCacheException {

    public function logMessage(LoggerInterface $logger = null) {

        if ($logger instanceof LoggerInterface) $logger->debug($this->getMessage());
    }

}
