<?php

namespace NGSOFT\Tools\Exceptions;

use Exception,
    NGSOFT\Tools\Interfaces\ExceptionInterface,
    Psr\Log\LoggerInterface;

class Exception extends Exception implements ExceptionInterface {

    public function logMessage(LoggerInterface $logger) {
        $logger->error($this->getMessage());
    }

}
