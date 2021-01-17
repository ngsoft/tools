<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use Psr\Log\LoggerAwareTrait;

trait LoggerAware {

    use LoggerAwareTrait;

    /**
     * Logs with an arbitrary level.
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    protected function log($level, string $message, array $context = []) {
        $this->logger and $this->logger->log($level, $message, $context);
    }

}
