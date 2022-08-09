<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\{
    Container\Exceptions\CircularDependencyException, Container\Exceptions\ContainerError, Container\Exceptions\NotFound, Container\Exceptions\ResolverException,
    Container\Resolvers\ContainerResolver, DataStructure\PrioritySet, Traits\StringableObject, Traits\Unserializable
};
use Psr\Container\ContainerInterface as PsrContainerInterface,
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
    protected array $loadedServices = [];

    /** @var Closure[] */
    protected array $definitions = [];

    /** @var bool[] */
    protected array $resolving = [];

    /** @var mixed[] */
    protected array $resolved = [];
    protected ParameterResolver $parameterResolver;
    protected PrioritySet $containerResolvers;

    public function __construct(
            iterable $definitions = []
    )
    {
        $this->parameterResolver = new ParameterResolver($this);
        $this->containerResolvers = PrioritySet::create();
        $this->set(__CLASS__, $this);
        // if extended
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
                                    '[%s] is aliased to itself.',
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
                $this->canResolve($abstract);
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        try {
            $this->loadService($id);
            return $this->resolved[$this->getAlias($id)] ??= $this->resolve($id);
        } catch (Throwable $prev) {
            throw NotFound::for($id, $prev);
        }
    }

    /** {@inheritdoc} */
    public function make(string $id, array $parameters = []): mixed
    {
        try {
            return $this->resolve($id, $parameters);
        } catch (Throwable $prev) {
            throw NotFound::for($id, $prev);
        }
    }

    /** {@inheritdoc} */
    public function call(object|array|string $callable, array $parameters = []): mixed
    {

        try {
            return $this->resolveCall($callable, $parameters);
        } catch (Throwable $prev) {
            throw new ContainerError('Cannot call callable: ' . (is_string($callable) ? $callable : var_export($callable, true)), previous: $prev);
        }
    }

    /** {@inheritdoc} */
    public function register(ServiceProvider $service): void
    {
        if (empty($service->provides())) {
            return;
        }

        foreach (array_unique($service->provides()) as $id) {
            $this->services[$id] = $service;
            unset($this->resolved[$id]);
        }
    }

    /** {@inheritdoc} */
    public function set(string $id, mixed $value): void
    {

        $abstract = $this->getAlias($id);
        unset($this->resolved[$abstract]);

        if ($value instanceof Closure) {
            $this->definitions[$abstract] = $value;
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
     * Add an handler to manage entry resolution
     *
     * @param ContainerResolver $resolver
     * @param int|null $priority
     */
    public function addContainerResolver(ContainerResolver $resolver, ?int $priority = null)
    {
        if ( ! $priority) {
            $priority = $resolver->getDefaultPriority();
        }
        $this->containerResolvers->add($resolver, $priority);
    }

    protected function loadService(string $id): void
    {

        if (
                ! isset($this->loadedServices[$id]) &&
                $provider = $this->services[$id] ?? null
        ) {

            $provider->register($this);
            foreach ($provider->provides() as $service) {
                $this->loadedServices[$service] = true;
            }
        }
    }

    protected function resolveCall(object|array|string $callable, array $providedParams): mixed
    {
        // Class@method(), Class::method()
        if (is_string($callable)) {
            $cm = preg_split('#[:@]+#', $callable);
            switch (count($cm)) {
                case 2:
                    $callable = $cm;
                    break;
                case 1:
                    $callable = $cm[0];
                    break;
                default :
                    throw new ContainerError('Invalid Callable: ' . $callable);
            }
        }

        return $this->parameterResolver->resolve($callable, $providedParams);
    }

    protected function resolve(string $id, array $providedParams = []): mixed
    {

        $resolving = &$this->resolving;

        $abstract = $this->getAlias($id);

        if (isset($resolving[$abstract])) {
            throw new CircularDependencyException(
                            sprintf(
                                    'Container is already resolving [%s].',
                                    $id
                            )
            );
        }

        $resolved = null;

        if ($this->canResolve($abstract)) {

            $resolving[$abstract] = true;
            $def = $this->definitions[$abstract] ?? null;

            if ($def instanceof \Closure) {
                $resolved = $this->parameterResolver->resolve($def, $providedParams);
            } elseif (is_instanciable($abstract)) {
                $resolved = $this->parameterResolver->resolve($abstract, $providedParams);
            }

            unset($resolving[$abstract]);
        }

        if (is_null($resolved)) {
            throw new ResolverException(
                            sprintf(
                                    'Cannot resolve [%s]',
                                    $id
                            )
            );
        }

        /** @var ContainerResolver $resolver */
        foreach ($this->containerResolvers as $resolver) {
            $resolved = $resolver->resolve($resolved);
        }

        return $resolved;
    }

    protected function canResolve(string $id): bool
    {
        return $this->parameterResolver->canResolve($id, $this->definitions[$id] ?? null);
    }

}
