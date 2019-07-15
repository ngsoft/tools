x<?php

namespace NGSOFT\Tools\Traits;

use Psr\Log\LoggerInterface;

trait ExceptionLoggerTrait {

    /**
     * Log The Exception Message
     * @param \NGSOFT\Tools\Traits\LoggerInterface $logger
     */
    public function logMessage(LoggerInterface $logger) {
        $logger->error($this->getMessage());
    }

}
