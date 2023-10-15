<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

interface_exists(LoggerAwareInterface::class);
class_exists(LogLevel::class);
class_exists(NullLogger::class);

trait LoggerAware
{
    use LoggerAwareTrait;

    /**
     * Get Logger instance.
     */
    public function getLogger(): LoggerInterface
    {
        if ( ! $this->logger)
        {
            $this->setLogger(new NullLogger());
        }
        return $this->logger;
    }

    /**
     * Logs with an arbitrary level.
     */
    protected function log(mixed $level, string $message, array $context = [])
    {
        $this->getLogger()->log($level, $message, $context);
    }
}
