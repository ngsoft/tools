<?php

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\Interfaces\ExceptionInterface;
use NGSOFT\Tools\Traits\Logger;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use RuntimeException;

class BasicCacheException extends RuntimeException implements \Psr\Cache\CacheException, \Psr\SimpleCache\CacheException, ExceptionInterface {

    use LoggerAwareTrait,
        Logger;

    public function logMessage(LoggerInterface $logger) {

        $this->setLogger($logger);
        $this->log($this->getMessage());
    }

}
