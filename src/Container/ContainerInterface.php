<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Psr\Container\ContainerInterface as PSRContainerInterface;

interface ContainerInterface extends PSRContainerInterface
{

    /**
     * Add an handler to manage entry resolution
     * eg: add an handler to autowire LoggerAware ...
     *
     * @param callable $handler
     * @return void
     */
    public function addResolutionHandler(callable $handler): void;

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
}
