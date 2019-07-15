<?php

namespace NGSOFT\Tools\Exceptions;

use InvalidArgumentException,
    NGSOFT\Tools\Interfaces\ExceptionInterface,
    Psr\Log\LoggerInterface;

class InvalidArgumentException extends InvalidArgumentException implements ExceptionInterface {

    public function logMessage(LoggerInterface $logger) {
        $logger->error($this->getMessage());
    }

}
