<?php

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\Interfaces\ExceptionInterface,
    Psr\Log\LoggerInterface;

class ErrorException extends ErrorException implements ExceptionInterface {

    public function logMessage(LoggerInterface $logger) {
        $logger->error($this->getMessage());
    }

}
