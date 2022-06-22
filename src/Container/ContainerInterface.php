<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{

    public const PRIORITY_LOW = 32;
    public const PRIORITY_MEDIUM = 64;
    public const PRIORITY_HIGH = 128;

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     *
     * @return mixed|null Entry.
     */
    public function tryGet(string $id): mixed;

    /**
     * Add an handler to manage entry resolution
     * eg: add an handler to autowire LoggerAware ...
     *
     * @param Closure|ContainerResolver $handler
     * @param int $priority
     * @return static
     */
    public function addResolutionHandler(Closure|ContainerResolver $handler, int $priority = self::PRIORITY_MEDIUM): static;

    /**
     * Register a service
     *
     * @param ServiceProvider $provider
     * @return static
     */
    public function register(ServiceProvider $provider): static;

    /**
     * Adds multiple definitions
     *
     * @param array $definitions
     * @return void
     */
    public function setMultiple(array $definitions): void;

    /**
     * Add a definition to the container
     *
     * @param string $id
     * @param mixed|Closure $entry
     * @return void
     */
    public function set(string $id, mixed $entry): void;

    /**
     * Alias an entry to a different name
     *
     * @param string|iterable $alias
     * @param string $id
     * @return static
     */
    public function alias(string|iterable $alias, string $id): static;
}
