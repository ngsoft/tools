<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\Traits\{
    StringableObject, Unserializable
};
use Stringable;
use function get_debug_type;

abstract class ContainerAbstract implements ContainerInterface, Stringable
{

    use StringableObject,
        Unserializable;

    /** @var callable[] */
    protected array $handlers = [];

    /** @var array<string, string> */
    protected array $alias = [];

    /** @var ServiceProvider[] */
    protected array $providers = [];
    protected bool $registering = false;

    public function __construct(
            protected array $definitions = []
    )
    {
        $this->definitions[ContainerInterface::class] = $this->definitions[static::class] = $this;
    }

    /** {@inheritdoc} */
    public function addResolutionHandler(Closure|ContainerResolver $handler): void
    {
        if (in_array($handler, $this->handlers)) {
            throw new ContainerResolverException('Cannot add the same resolver twice.');
        }
        $this->handlers[] = $handler;
    }

    abstract protected function isResolved(string $id): bool;

    /**
     * Execute handlers when resolving the entry
     */
    protected function resolve(string $id, mixed $resolved): mixed
    {
        foreach ($this->handlers as $handler) {
            $resolved = $handler($this, $id, $resolved);
        }
        return $resolved;
    }

    /**
     * Lazy loads services providers
     */
    protected function handleServiceProvidersResolution(string $id): void
    {
        $this->registering = true;
        $this->providers[$id]?->register($this);
        $this->registering = false;
    }

    /**
     * Alias resolution
     */
    protected function handleAliasResolution(string $alias): string
    {
        return $this->alias[$alias] ?? $alias;
    }

    /** {@inheritdoc} */
    public function register(ServiceProvider $provider): void
    {
        foreach ($provider->provides() as $id) {
            $this->providers[$id] = $provider;
        }
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        $id = $this->handleAliasResolution($id);

        if ( ! $this->isResolved($id)) {
            $this->definitions[$id] = $this->resolve($id, $this->definitions[$id] ?? null);
        }
        return $this->definitions[$id];
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
        $id = $this->handleAliasResolution($id);
        $this->definitions[$id] = $entry;
        if ($this->registering) {
            unset($this->providers[$id]);
        }
    }

    /** {@inheritdoc} */
    public function alias(string $id, string $alias): void
    {
        $this->alias[$alias] = $id;
    }

    /** {@inheritdoc} */
    public function extend(string $id, Closure $closure): void
    {
        if ( ! $this->has($id)) {
            throw new NotFoundException($this, $id);
        }

        $current = $this->get($id);
        $obj = is_object($current);
        $type = get_debug_type($current);

        $new = $closure($this, $current);

        $newType = get_debug_type($new);

        if ($obj ?  ! is_a($new, $type) : $new !== $newType) {
            throw new ContainerResolverException(sprintf(
                                    '%s::%s() invalid closure return value, %s expected, %s given.',
                                    static::class, __FUNCTION__,
                                    $type, $newType)
            );
        }
        $this->definitions[$id] = $new;
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return array_keys($this->definitions);
    }

}
