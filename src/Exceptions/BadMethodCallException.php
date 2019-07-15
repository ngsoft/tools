<?php

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\Interfaces\ExceptionInterface,
    Psr\Log\LoggerInterface;

class BadMethodCallException extends BadMethodCallException implements ExceptionInterface {

    public function logMessage(LoggerInterface $logger) {

        $logger->debug($this->getMessage());
    }

}
