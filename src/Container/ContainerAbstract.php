<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use ArrayAccess,
    Closure;
use NGSOFT\{
    Container\Resolvers\LoggerAwareResolver, Container\Resolvers\NotFoundResolver, Traits\StringableObject, Traits\Unserializable
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
    ];

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
        $this->set(static::class, $this);
        $this->alias([PsrContainerInterface::class, ContainerInterface::class, 'Container'], static::class);

        foreach (self::BASIC_RESOLVERS as $resolver) {
            $this->addResolutionHandler(new $resolver());
        }
    }

    /** {@inheritdoc} */
    public function addResolutionHandler(Closure|ContainerResolver $handler): static
    {
        if (in_array($handler, $this->handlers)) {
            throw new ContainerResolverException('Cannot add the same resolver twice.');
        }
        $this->handlers[] = $handler;

        return $this;
    }

    abstract protected function isResolved(string $id): bool;

    /**
     * Execute handlers when resolving the entry
     */
    protected function resolve(string $id, mixed $resolved): mixed
    {
        foreach (array_reverse($this->handlers) as $handler) {
            $resolved = $handler($this, $id, $resolved);
        }
        return $resolved;
    }

    /**
     * Lazy loads services providers
     */
    protected function handleServiceProvidersResolution(string $id): void
    {

        if (isset($this->providers[$id])) {
            $this->registering = true;
            $this->providers[$id]->register($this);
            $this->registering = false;
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
        if ( ! $this->hasServiceProvider($provider)) {
            foreach ($provider->provides() as $id) {
                $this->providers[$id] = $provider;
            }
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function hasServiceProvider(ServiceProvider $provider): bool
    {

        $provides = $provider->provides();
        if (empty($provides)) {
            return false;
        }


        /** @var ServiceProvider $registered */
        foreach ($provides as $id) {
            if ( ! isset($this->providers[$id])) {
                return false;
            }

            $registered = $this->providers[$id];
            if (get_class($registered) !== get_class($provider)) {
                return false;
            }

            if ($registered->provides() !== $provides) {
                return false;
            }
        }


        return true;
    }

    /** {@inheritdoc} */
    public function hasEntry(string $id): bool
    {
        $id = $this->handleAliasResolution($id);
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers);
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
        $id = $this->handleAliasResolution($id);

        $this->definitions[$id] = $entry;
        if ($this->registering) {
            unset($this->providers[$id]);
        }
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
        $keys = array_keys($this->entries());
        return array_combine($keys, $keys);
    }

}
