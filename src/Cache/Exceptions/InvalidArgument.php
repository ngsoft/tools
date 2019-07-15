<?php

namespace NGSOFT\Tools\Cache\Exceptions;

use Psr\Log\LoggerInterface;

class InvalidArgument extends \InvalidArgumentException implements \Psr\Cache\InvalidArgumentException, \Psr\SimpleCache\InvalidArgumentException {

    public function logMessage(LoggerInterface $logger = null) {

        if ($logger instanceof LoggerInterface) $logger->debug($this->getMessage());
    }

}
