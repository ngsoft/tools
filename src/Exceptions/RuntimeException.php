<?php

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\Interfaces\ExceptionInterface,
    Psr\Log\LoggerInterface;

class RuntimeException extends RuntimeException implements ExceptionInterface {

    public function logMessage(LoggerInterface $logger) {
        $logger->error($this->getMessage());
    }

}
