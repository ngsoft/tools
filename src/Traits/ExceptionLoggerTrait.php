<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Traits;

use Psr\Log\LoggerInterface;

trait ExceptionLoggerTrait {

    /**
     * Log The Exception Message
     * @param LoggerInterface $logger
     */
    public function logMessage(LoggerInterface $logger) {
        if ($this instanceof \Throwable) $logger->debug($this->getMessage());
    }

}
