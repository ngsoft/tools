<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use ArrayAccess,
    Closure;
use NGSOFT\{
    Container\Resolvers\LoggerAwareResolver, Container\Resolvers\NotFoundResolver, Container\Resolvers\SimpleClosureResolver, DataStructure\PrioritySet,
    Traits\StringableObject, Traits\Unserializable
};
use Psr\Container\{
    ContainerExceptionInterface, ContainerInterface as PsrContainerInterface
};
use Stringable;

abstract class ContainerAbstract implements ContainerInterface, Stringable, ArrayAccess
{

    protected const BASIC_RESOLVERS = [
        NotFoundResolver::class,
        LoggerAwareResolver::class,
        SimpleClosureResolver::class,
    ];

    use StringableObject,
        Unserializable;

    /** @var PrioritySet<callable> */
    protected PrioritySet $handlers;

    /** @var array<string, true> */
    protected array $resolved = [];

    /** @var array<string, string> */
    protected array $alias = [];

    /** @var array<string, ServiceProvider> */
    protected array $providers = [];
    protected array $registered = [];

    public function __construct(
            protected array $definitions = []
    )
    {
        $this->handlers = new PrioritySet();

        $this->set(static::class, $this);
        $this->alias([PsrContainerInterface::class, ContainerInterface::class, 'Container'], static::class);

        foreach (self::BASIC_RESOLVERS as $resolver) {
            $this->addResolutionHandler(new $resolver());
        }
    }

    /** {@inheritdoc} */
    public function addResolutionHandler(Closure|ContainerResolver $handler, int $priority = self::PRIORITY_MEDIUM): static
    {

        if ($this->handlers->has($handler)) {
            throw new ContainerResolverException('Cannot add the same resolver twice.');
        }
        if ($handler instanceof ContainerResolver) {
            $priority = $priority === self::PRIORITY_MEDIUM ? $handler->getDefaultPriority() : $priority;
        }

        $this->handlers->add($handler, $priority);
        return $this;
    }

    /**
     * Entry is resolved ?
     */
    protected function isResolved(string $id): bool
    {
        return $this->resolved[$id] ?? false;
    }

    /**
     * Execute handlers when resolving the entry
     */
    protected function resolve(string $id, mixed $resolved): mixed
    {
        if ($this->isResolved($id)) {
            return $resolved;
        }

        foreach ($this->handlers as $handler) {
            $resolved = $handler($this, $id, $resolved);
        }

        // with NotFoundResolver we never get here
        if ($resolved !== null) {
            $this->resolved[$id] = true;
        }

        return $resolved;
    }

    /**
     * Lazy loads services providers
     */
    protected function handleServiceProvidersResolution(string $id): void
    {
        if ( ! isset($this->registered[$id]) && isset($this->providers[$id])) {
            $provider = $this->providers[$id];
            $provider->register($this);
            foreach ($provider->provides() as $id) {
                $this->registered[$id] = true;
            }
        }
    }

    /**
     * Alias resolution
     */
    protected function handleAliasResolution(string $alias): string
    {
        return isset($this->alias[$alias]) ? $this->handleAliasResolution($this->alias[$alias]) : $alias;
    }

    /** {@inheritdoc} */
    public function register(ServiceProvider $provider): static
    {
        foreach ($provider->provides() as $id) {
            $this->providers[$id] = $provider;
            unset($this->registered[$id]);
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return $this->tryGet($id) !== null;
    }

    /** {@inheritdoc} */
    public function tryGet(string $id): mixed
    {
        try {
            return $this->get($id);
        } catch (ContainerExceptionInterface) {
            return null;
        }
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {

        $this->handleServiceProvidersResolution($id);
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
        $this->definitions[$this->handleAliasResolution($id)] = $entry;
    }

    /** {@inheritdoc} */
    public function alias(string|iterable $alias, string $id): static
    {
        if ( ! is_iterable($alias)) {
            $alias = [$alias];
        }
        foreach ($alias as $alias) {
            $this->alias[$alias] = $id;
        }
        return $this;
    }

    protected function entries(): array
    {

        $entries = $this->definitions;
        $entries += $this->alias;
        $entries += $this->providers;
        return $entries;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->definitions[$offset], $this->alias[$offset], $this->providers[$offset]);
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return array_keys($this->entries());
    }

}
