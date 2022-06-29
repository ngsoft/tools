<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\{
    Container\Exceptions\CircularDependencyException, Container\Exceptions\NotFound, Container\Exceptions\ResolverException, Container\Resolvers\ProvidedClosureResolver,
    DataStructure\PrioritySet, Traits\StringableObject, Traits\Unserializable
};
use Psr\Container\ContainerInterface as PsrContainerInterface,
    Symfony\Component\Translation\Exception\LogicException,
    Throwable;
use function is_instanciable;

class Container implements ContainerInterface
{

    use StringableObject,
        Unserializable;

    /** @var array<string, string> */
    protected array $aliases = [];

    /** @var ServiceProvider[] */
    protected array $services = [];

    /** @var bool[] */
    protected array $registered = [];

    /** @var Closure[] */
    protected array $definitions = [];

    /** @var bool[] */
    protected array $resolving = [];

    /** @var mixed[] */
    protected array $resolved = [];

    /** @var PrioritySet<ContainerResolver> */
    protected PrioritySet $resolvers;

    /** @var Closure[] */
    protected array $resolverClosures = [];
    protected bool $locked = false;

    public function __construct(
            iterable $definitions = []
    )
    {

        $this->resolvers = new PrioritySet();

        $this->set(static::class, $this);
        $this->alias([PsrContainerInterface::class, ContainerInterface::class, 'Container'], static::class);

        $this->setMany($definitions);
    }

    /** {@inheritdoc} */
    public function alias(string|array $alias, string $id): void
    {
        $alias = array_unique((array) $alias);

        if (in_array($id, $alias)) {
            throw new ContainerError(sprintf(
                                    '%s is aliased to itself.',
                                    $id
            ));
        }


        $this->aliases += array_fill_keys($alias, $id);
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
        $abstract = $this->getAlias($id);
        return
                array_key_exists($abstract, $this->resolved) ||
                array_key_exists($abstract, $this->services) ||
                array_key_exists($abstract, $this->definitions) ||
                $this->canResolve($abstract);
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        try {
            return $this->resolved[$id] ??= $this->resolve($id);
        } catch (Throwable $prev) {
            throw NotFound::for($id, $prev);
        }
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
            $abstract = $this->getAlias($id);
            $this->services[$abstract] = $service;
            unset($this->resolved[$abstract]);
        }
    }

    /** {@inheritdoc} */
    public function set(string $id, mixed $value): void
    {

        $abstract = $this->getAlias($id);

        if ($value instanceof Closure) {
            $this->definitions[$abstract] = $value;
            unset($this->resolved[$abstract]);
            return;
        }
        $this->resolved[$abstract] = $value;
    }

    /** {@inheritdoc} */
    public function setMany(iterable $definitions): void
    {
        foreach ($definitions as $id => $value) {
            $this->set($id, $value);
        }
    }

    /**
     * Add a resolution handler
     */
    public function addResolutionHandler(ContainerResolver|Closure $handler, int $priority = ContainerResolver::PRIORITY_MEDIUM): static
    {

        if (in_array($handler, $this->resolverClosures, true) || $this->resolvers->has($handler)) {
            throw ResolverException::notTwice($handler);
        }

        if ($handler instanceof Closure) {
            $this->resolverClosures[] = $handler;
            $handler = new ProvidedClosureResolver($handler, $priority);
        }


        if ($priority === ContainerResolver::PRIORITY_MEDIUM) {
            $priority = $handler->getDefaultPriority();
        }

        $this->resolvers->add($handler, $priority);

        return $this;
    }

    protected function loadService(string $id): void
    {

        if ( ! isset($this->registered[$id])) {
            if (isset($this->services[$id])) {

                $this->services[$id]->register($this);
                foreach ($this->services[$id]->provides() as $service) {
                    $this->registered[$service] = true;
                }
            }
        }
    }

    protected function resolve(string $id, array $providedParams = []): mixed
    {
        $this->loadService($id);
        $abstract = $this->getAlias($id);

        if (isset($this->resolving[$abstract])) {
            throw new CircularDependencyException(
                            sprintf(
                                    'Container is already resolving %s, cannot resolve it twice in the same loop.',
                                    $id
                            )
            );
        }

        $this->resolving[$abstract] = $this->resolving[$id] = true;

        $resolved = $this->definitions[$abstract] ?? null;

        /** @var ContainerResolver $resolver */
        foreach ($this->resolvers as $resolver) {
            $resolved = $resolver->resolve($this, $abstract, $resolved, $providedParams);
        }

        unset($this->resolving[$id], $this->resolving[$abstract]);
        if (is_null($resolved)) {
            throw new ResolverException(
                            sprintf(
                                    'Cannot resolve %s',
                                    $id
                            )
            );
        }

        return $resolved;
    }

    protected function canResolve(string $id): bool
    {
        return is_instanciable($id);
    }

}
