<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use NGSOFT\Traits\StringableObject,
    Psr\Container\ContainerInterface,
    Stringable;

abstract class Container implements ContainerInterface, Stringable
{

    use StringableObject;

    public function __construct(
            protected array $definitions = []
    )
    {
        $this->definitions[ContainerInterface::class] = $this->definitions[static::class] = $this;
    }

    /**
     * Register a service
     *
     * @param ServiceProvider $provider
     * @return void
     */
    public function register(ServiceProvider $provider): void
    {
        $provider->provide($this);
    }

    /**
     * Adds multiple definitions
     *
     * @param array $definitions
     * @return void
     */
    public function setMultiple(array $definitions): void
    {
        foreach ($definitions as $id => $entry) {
            $this->set($id, $entry);
        }
    }

    /**
     * Add a definition to the container
     *
     * @param string $id
     * @param mixed $entry
     * @return void
     */
    public function set(string $id, mixed $entry): void
    {
        $this->definitions[$id] = $entry;
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return array_keys($this->definitions);
    }

}
