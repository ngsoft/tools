<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Traits\StringableObject,
    Stringable;

abstract class ContainerAbstract implements ContainerInterface, Stringable
{

    use StringableObject;

    /** @var Closure[] */
    protected array $handlers = [];

    public function __construct(
            protected array $definitions = []
    )
    {
        $this->definitions[ContainerInterface::class] = $this->definitions[static::class] = $this;
    }

    /** {@inheritdoc} */
    public function addResolutionHandler(callable $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * Execute handlers when resolving the entry
     *
     * @param mixed $resolved
     * @return mixed
     */
    protected function handle(mixed $resolved): mixed
    {
        foreach ($this->handlers as $handler) {
            $resolved = $handler($this, $resoved);
        }
        return $resolved;
    }

    /** {@inheritdoc} */
    public function register(ServiceProvider $provider): void
    {
        $provider->provide($this);
    }

    /** {@inheritdoc} */
    public function setMultiple(array $definitions): void
    {
        foreach ($definitions as $id => $entry) {
            $this->set($id, $entry);
        }
    }

    /** {@inheritdoc} */
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
