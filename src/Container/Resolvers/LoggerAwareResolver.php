<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use NGSOFT\Container\{
    ContainerInterface, ContainerResolver
};
use Psr\Log\{
    LoggerAwareInterface, LoggerInterface
};

/** @phan-file-suppress PhanUnusedPublicMethodParameter */
class LoggerAwareResolver implements ContainerResolver
{

    protected ?bool $logger = null;

    public function __invoke(ContainerInterface $container, string $id, mixed $value): mixed
    {

        $this->logger = $this->logger ?? $container->has(LoggerInterface::class);

        if ($value instanceof LoggerAwareInterface && $this->logger) {
            $value->setLogger($container->get(LoggerInterface::class));
        }

        return $value;
    }

    public function getDefaultPriority(): int
    {
        return ContainerInterface::PRIORITY_LOW;
    }

}
