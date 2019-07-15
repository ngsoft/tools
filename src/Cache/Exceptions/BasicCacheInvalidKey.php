<?php

namespace NGSOFT\Tools\Exceptions;

use InvalidArgumentException;
use NGSOFT\Tools\Interfaces\ExceptionInterface;
use NGSOFT\Tools\Traits\Logger;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class BasicCacheInvalidKey extends InvalidArgumentException implements \Psr\Cache\InvalidArgumentException, \Psr\SimpleCache\InvalidArgumentException, ExceptionInterface {

    use LoggerAwareTrait,
        Logger;

    public function logMessage(LoggerInterface $logger) {

        $this->setLogger($logger);
        $this->log($this->getMessage());
    }

}
