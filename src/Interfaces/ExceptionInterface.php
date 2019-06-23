<?php

namespace NGSOFT\Tools\Interfaces;

use Psr\Log\LoggerInterface;

interface ExceptionInterface extends LogWriterInterface {

    /**
     * Log the message
     */
    public function logMessage(LoggerInterface $logger);
}
