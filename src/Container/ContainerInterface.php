<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{

    /**
     * Checks if container has the requested entry physically
     *
     * @param string $id
     * @return bool
     */
    public function hasEntry(string $id): bool;

    /**
     * Add an handler to manage entry resolution
     * eg: add an handler to autowire LoggerAware ...
     *
     * @param Closure|ContainerResolver $handler
     * @return static
     */
    public function addResolutionHandler(Closure|ContainerResolver $handler): static;

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
