<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Traits\StringableObject,
    Stringable;

abstract class ContainerAbstract implements ContainerInterface, Stringable
{

    use StringableObject;

    /** @var callable[] */
    protected array $handlers = [];

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

    protected function handleServiceProvidersResolution(string $id): void
    {
        $this->registering = true;
        $this->providers[$id]?->register($this);
        $this->registering = false;
    }

    /** {@inheritdoc} */
    public function register(ServiceProvider $provider): void
    {
        foreach ($provider->provides() as $id) {
            $this->providers[$id] = $provider;
        }
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
        if ($this->registering) {
            unset($this->providers[$id]);
        }
    }

    /** {@inheritdoc} */
    public function alias(string $id, string $alias): void
    {
        if ( ! $this->has($id)) {
            throw new NotFoundException($this, $id);
        }

        $this->set($alias, $this->get($id));
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

        $error = false;

        switch ($obj) {
            case true:
                $error = ! is_a($new, $type);
                break;
            case false:
                $error = $new !== $newType;
                break;
        }


        if ($error) {
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
