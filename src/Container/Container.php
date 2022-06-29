<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\Traits\{
    StringableObject, Unserializable
};
use Psr\Container\ContainerInterface as PsrContainerInterface;

class Container implements ContainerInterface
{

    use StringableObject,
        Unserializable;

    protected array $aliases = [];
    protected array $services = [];
    protected array $definitions = [];
    protected array $resolved = [];
    protected bool $locked = false;

    public function __construct(
            iterable $definitions = []
    )
    {

        $this->set(static::class, $this);
        $this->alias([PsrContainerInterface::class, ContainerInterface::class, 'Container'], static::class);

        $this->setMany($definitions);
    }

    /** {@inheritdoc} */
    public function alias(string|array $alias, string $id): void
    {
        $this->aliases += array_fill_keys(array_unique((array) $alias), $id);
    }

    /**
     * Resolves alias
     */
    protected function getAlias(string $id): string
    {
        return isset($this->aliases[$id]) ? $this->getAlias($this->aliases[$id]) : $id;
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        $id = $this->getAlias($id);
        return
                array_key_exists($id, $this->resolved) ||
                array_key_exists($id, $this->services) ||
                array_key_exists($id, $this->definitions) ||
                $this->canResolve($id);
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        $id = $this->getAlias($id);
        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }

        return null;
    }

    /** {@inheritdoc} */
    public function make(string $id, array $parameters = []): mixed
    {
        return null;
    }

    /** {@inheritdoc} */
    public function call(callable|array|string $callable, array $parameters = []): mixed
    {
        return null;
    }

    /** {@inheritdoc} */
    public function register(ServiceProvider $service): void
    {
        if (empty($service->provides())) {
            return;
        }

        foreach (array_unique($service->provides()) as $id) {
            $id = $this->getAlias($id);
            $this->services[$id] = $service;

            unset($this->resolved[$id]);
        }
    }

    /** {@inheritdoc} */
    public function set(string $id, mixed $value): void
    {

        $id = $this->getAlias($id);

        if ($value instanceof Closure) {
            $this->definitions[$id] = $value;
            unset($this->resolved[$id]);
            return;
        }
        $this->resolved[$id] = $value;
    }

    /** {@inheritdoc} */
    public function setMany(iterable $definitions): void
    {
        foreach ($definitions as $id => $value) {
            $this->set($id, $value);
        }
    }

    protected function canResolve(string $id): bool
    {
        return is_instanciable($id);
    }

}
