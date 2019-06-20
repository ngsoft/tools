<?php

namespace NGSOFT\Tools\Traits;

use Psr\Log\LoggerInterface;

/**
 * To use in conjunction with \Psr\Log\LoggerAwareTrait
 */
trait LoggerAwareWriter {

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Checks if logger is defined and logs a debug message
     * @param string $message
     */
    public function __log(string $message) {
        if ($this->logger instanceof LoggerInterface) $this->logger->debug($message);
    }

}
