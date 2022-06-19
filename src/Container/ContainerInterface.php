<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{

    /**
     * Add an handler to manage entry resolution
     * eg: add an handler to autowire LoggerAware ...
     *
     * @param Closure|ContainerResolver $handler
     * @return void
     */
    public function addResolutionHandler(Closure|ContainerResolver $handler): void;

    /**
     * Register a service
     *
     * @param ServiceProvider $provider
     * @return void
     */
    public function register(ServiceProvider $provider): void;

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
     * @param string $id
     * @param string $alias
     * @return void
     */
    public function alias(string $id, string $alias): void;

    /**
     * Extends an entry from the container
     *
     * @param string $id
     * @param Closure $closure must return the same type
     * @return void
     */
    public function extend(string $id, Closure $closure): void;
}
