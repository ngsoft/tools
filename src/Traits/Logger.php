<?php

namespace NGSOFT\Tools\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * To use in conjunction with \Psr\Log\LoggerAwareTrait
 */
trait Logger {

    /** @var LoggerInterface */
    protected $logger;

    /**
     * Dispatches the message to the logger if detected
     * @param string $message
     * @param mixed $level
     * @param array $context
     * @return void
     */
    public function log(string $message, $level = LogLevel::DEBUG, array $context = []) {
        if ($this->logger instanceof LoggerInterface) $this->logger->log($level, $message, $context);
    }

}
