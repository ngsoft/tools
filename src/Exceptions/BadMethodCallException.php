<?php

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\Interfaces\ExceptionInterface;
use NGSOFT\Tools\Traits\Logger;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface {

    use LoggerAwareTrait,
        Logger;

    public function logMessage(LoggerInterface $logger) {
        $this->setLogger($logger);
        $this->log($this->getMessage(), LogLevel::CRITICAL);
    }

}
