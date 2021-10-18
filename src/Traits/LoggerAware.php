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
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Logs with an arbitrary level.
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    protected function log($level, string $message, array $context = []) {

        $this->getLogger()->log($level, $message, $context);
    }

    /**
     * Get Logger instance
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface {
        if (!$this->logger) $this->setLogger(new NullLogger());
        return $this->logger;
    }

}
