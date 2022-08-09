<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

abstract class ContainerResolver
{

    public const PRIORITY_LOW = 32;
    public const PRIORITY_MEDIUM = 64;
    public const PRIORITY_HIGH = 128;

    /**
     * Set the default priority
     *
     * @return int
     */
    public function getDefaultPriority(): int
    {
        return self::PRIORITY_MEDIUM;
    }

    /**
     * Resolves an entry from the container
     */
    abstract public function resolve(mixed $value): mixed;
}
