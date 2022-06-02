<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    protected ContainerResolver $resolver;

    public function __construct(
            protected array $definitions = []
    )
    {
        $this->resolver = new ContainerResolver();
        $this->definitions[ContainerInterface::class] = $this->definitions[__CLASS__] = $this;
    }

    public function set(string $id, mixed $entry): void
    {
        $this->definitions[$id] = $entry;
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        if (!$this->isResolved($id)) $this->definitions[$id] = $this->resolver->resolve($id, $this->definitions[$id] ?? null, $this);
        return $this->definitions[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || class_exists($id);
    }

    protected function isResolved(string $id): bool
    {
        if (array_key_exists($id, $this->definitions)) {
            return $this->definitions[$id] instanceof Closure === false;
        } elseif (class_exists($id)) {
            return false;
        }
        throw new NotFoundException($this, $id);
    }

    public function __debugInfo(): array
    {
        return array_keys($this->definitions);
    }

}
