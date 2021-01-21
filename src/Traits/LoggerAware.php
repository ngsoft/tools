<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use Psr\Log\{
    LoggerAwareInterface, LoggerAwareTrait, LogLevel, NullLogger
};

interface_exists(LoggerAwareInterface::class);
class_exists(LogLevel::class);
class_exists(NullLogger::class);

trait LoggerAware {

    use LoggerAwareTrait;

    /**
     * Logs with an arbitrary level.
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    protected function log($level, string $message, array $context = []) {
        if (!$this->logger) $this->setLogger(new NullLogger());
        $this->logger->log($level, $message, $context);
    }

}
