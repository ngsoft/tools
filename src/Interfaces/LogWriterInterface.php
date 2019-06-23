<?php

namespace NGSOFT\Tools\Interfaces;

use Psr\Log\LoggerAwareInterface;

interface LogWriterInterface extends LoggerAwareInterface {

    /**
     * Dispatches the message to the logger if detected
     * @param string $message
     * @param mixed $level
     * @param array $context
     * @return void
     */
    public function log(string $message, $level = LogLevel::DEBUG, array $context = []);
}
