<?php

declare(strict_types=1);

namespace NGSOFT\Container;

interface ContainerResolver
{

    public const PRIORITY_LOW = 32;
    public const PRIORITY_MEDIUM = 64;
    public const PRIORITY_HIGH = 128;

    /**
     * Resolves an entry from the container
     */
    public function resolve(ContainerInterface $container, string $id, mixed $value, array &$providedParams = []): mixed;

    /**
     * Set the default priority
     */
    public function getDefaultPriority(): int;

    /**
     * Resolver can resolve ?
     */
    public function canResolve(string $id, mixed $value): bool;
}
