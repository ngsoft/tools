<?php

namespace NGSOFT\Tools\Interfaces;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

interface LogWriterInterface extends LoggerAwareInterface {

    /** PSR Log Levels */
    const LOGLEVELS = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG
    ];

    /**
     * Dispatches the message to the logger if detected
     * @param string $message
     * @param mixed $level
     * @param array $context
     * @return void
     */
    public function log(string $message, $level = LogLevel::DEBUG, array $context = []);
}
