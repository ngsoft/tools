<?php

namespace NGSOFT\Tools\Exceptions;

class ErrorException extends \ErrorException implements \NGSOFT\Tools\Interfaces\ExceptionInterface {

    use LoggerAwareTrait,
        Logger;

    public function logMessage(LoggerInterface $logger) {
        $this->setLogger($logger);
        $this->log($this->getMessage(), LogLevel::CRITICAL);
    }

}
