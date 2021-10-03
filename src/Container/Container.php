<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use NGSOFT\{
    Events\EventDispatcher, Events\EventListener, Exceptions\NotFoundException
};
use Psr\{
    Container\ContainerInterface, EventDispatcher\EventDispatcherInterface, EventDispatcher\ListenerProviderInterface
};
use Symfony\Component\EventDispatcher\EventDispatcher as SEventDispatcher;

/**
 * Basic Container with autowiring
 */
final class Container implements ContainerInterface {

    /** @var array<string,mixed> */
    private $storage = [];

    /** @var Resolver */
    private $resolver;

    /**
     * @param array<string,mixed> $definitions
     */
    public function __construct(array $definitions = []) {
        $this->storage = $definitions;
        //define container
        $this->storage[ContainerInterface::class] = $this->storage[Container::class] = $this;
        $this->resolver = new Resolver($this);
    }

    /**
     * Add an Entry
     * @param string $id
     * @param mixed $value
     * @return static
     */
    public function set(string $id, $value): self {
        // cannot overwrite data
        if (!isset($this->storage[$id])) {
            $this->storage[$id] = $value;
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function get(string $id) {
        if (!$this->has($id)) {
            throw new NotFoundException($id, $this);
        }
        if (
                !isset($this->storage[$id])
        ) {

            if (
                    class_exists($id) and
                    $resolved = $this->resolver->resolveClassName($id)
            ) {
                return $this->storage[$id] = $resolved;
            }
            throw new NotFoundException($id, $this);
        } elseif (
                !is_object($this->storage[$id]) and
                is_callable($this->storage[$id])
        ) {
            $callable = $this->storage[$id];

            if ($resolved = $this->resolver->resolveCallable($callable)) {
                return $this->storage[$id] = $resolved;
            }
            throw new NotFoundException($id, $this);
        }
        return $this->storage[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id) {
        return
                isset($this->storage[$id]) or
                class_exists($id);
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        // do not bloat var_dumps
        return [];
    }

}
