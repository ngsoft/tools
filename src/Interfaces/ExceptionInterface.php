<?php

namespace NGSOFT\Tools\Interfaces;

use Psr\Log\LoggerInterface;

interface ExceptionInterface {

    /**
     * Log the message
     * @param LoggerInterface $logger
     */
    public function logMessage(LoggerInterface $logger);
}
