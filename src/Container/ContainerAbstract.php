<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\{
    Container\Resolvers\ClassStringResolver, Container\Resolvers\LoggerAwareResolver, Container\Resolvers\NotFoundResolver, Traits\StringableObject, Traits\Unserializable
};
use Psr\Container\ContainerInterface as PsrContainerInterface,
    Stringable;
use function get_debug_type;

abstract class ContainerAbstract implements ContainerInterface, Stringable
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
        $this->definitions[PsrContainerInterface::class] = $this->definitions[ContainerInterface::class] = $this->definitions[static::class] = $this->definitions['Container'] = $this;

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
        return $this->alias[$alias] ?? $alias;
    }

    /** {@inheritdoc} */
    public function register(ServiceProvider $provider): static
    {
        foreach ($provider->provides() as $id) {
            $this->providers[$id] = $provider;
        }
        return $this;
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

    /** {@inheritdoc} */
    public function extend(string $id, Closure $closure): static
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
        return $this;
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return array_keys($this->definitions);
    }

}
