<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use Psr\Log\{
    LoggerAwareInterface, LoggerInterface
};

/**
 * Injects Logger
 */
class LoggerAwareResolver extends ContainerResolver
{

    public function resolve(mixed $value): mixed
    {

        if ($value instanceof LoggerAwareInterface) {
            $value->setLogger($this->container->get(LoggerInterface::class));
        }

        return $value;
    }

    public function getDefaultPriority(): int
    {
        return self::PRIORITY_LOW;
    }

}
