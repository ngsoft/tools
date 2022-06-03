<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Psr\Container\ContainerInterface,
    Stringable;

abstract class Container implements ContainerInterface, Stringable
{

    public function __construct(
            protected array $definitions = []
    )
    {
        $this->definitions[ContainerInterface::class] = $this->definitions[static::class] = $this;
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

    /** {@inheritdoc} */
    public function __toString()
    {
        return sprintf('object(%s)#%d', get_class($this), spl_object_id($this));
    }

}
