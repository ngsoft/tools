<?php

namespace NGSOFT\Tools\Interfaces;

use Psr\Log\LoggerInterface;

interface ExceptionInterface {

    /**
     * Log the message
     * @param LoggerInterface|null $logger
     */
    public function logMessage(LoggerInterface $logger = null);
}
