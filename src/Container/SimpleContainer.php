<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface;

/**
 * Container with only basic functionality
 */
final class SimpleContainer implements Container
{

    private array $entries = [];

    public function __construct(array $definitions = [])
    {
        $definitions[ContainerInterface::class] = $definitions[ContainerInterface::class] ?? $this;
        $definitions[SimpleContainer::class] = $definitions[SimpleContainer::class] ?? $this;
        $this->entries = $definitions;
    }

    /**
     * Adds an entry to the container
     *
     * @param string $key
     * @param mixed $entry
     * @return void
     */
    public function set(string $key, mixed $entry): void
    {
        $this->entries[$key] = $entry;
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        if (!$this->isResolved($id)) { $this->entries[$id] = call_user_func($this->entries[$id], $this); }
        return $this->entries[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }

    private function isResolved(string $key): bool
    {
        if (!$this->has($key)) {
            throw new NotFoundException($this, $key);
        }

        return $this->entries[$key] instanceof Closure === false;
    }

}
