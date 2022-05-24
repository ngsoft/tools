<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface,
    ReflectionClass,
    ReflectionException;

/**
 * Basic Container with autowiring
 */
final class Container implements ContainerInterface {

    /** @var array<string,mixed> */
    private array $storage = [];

    /** @var array<string,callable|Closure> */
    private array $definitions = [];

    /** @var Resolver */
    private Resolver $resolver;

    /**
     * @param array<string,mixed> $definitions
     */
    public function __construct(array $definitions = []) {
        $this->resolver = new Resolver($this);

        $this->definitions = $definitions;
        $this->storage[ContainerInterface::class] = $this->storage[Container::class] = $this;
    }

    /**
     * Add an Entry/Definition to the container
     * @param string $id
     * @param mixed $value
     * @return static
     */
    public function set(string $id, mixed $value): self {
        // cannot overwrite data
        if (!isset($this->storage[$id])) {
            if ($this->isCallable($value)) {
                $this->definitions[$id] = $value;
            } else $this->storage[$id] = $value;
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function get(string $id) {
        if (!$this->has($id)) {
            throw new NotFoundException($id, $this);
        }
        if (is_null($this->storage[$id] ?? null)) {
            if (!is_null($this->definitions[$id] ?? null )) {
                if ($this->isCallable($this->definitions[$id])) {
                    if ($resolved = $this->resolver->resolveCallable($this->definitions[$id])) $this->storage[$id] = $resolved;
                    else throw new NotFoundException($id, $this);
                } elseif (is_scalar($this->definitions[$id] || is_object($this->definitions[$id]))) $this->storage[$id] = $this->definitions[$id];
                else throw new NotFoundException($id, $this);
            } elseif (
                    class_exists($id) &&
                    $resolved = $this->resolver->resolveClassName($id)
            ) $this->storage[$id] = $resolved;
            else throw new NotFoundException($id, $this);
        }
        return $this->storage[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id): bool {
        return
                !is_null($this->storage[$id] ?? null) ||
                !is_null($this->definitions[$id] ?? null) ||
                $this->isValidClass($id);
    }

    /** @return Resolver */
    public function getResolver(): Resolver {
        return $this->resolver;
    }

    /**
     * Checks if class is valid
     * @param string $className
     * @return bool
     */
    private function isValidClass(string $className): bool {

        if (class_exists($className)) {
            try {
                $reflector = new ReflectionClass($className);
                return $reflector->isInstantiable();
            } catch (ReflectionException) {

            }
        }

        return false;
    }

    private function isCallable(mixed $input): bool {
        return
                is_callable($input) &&
                !(is_object($input) && !($input instanceof Closure));
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        // do not bloat var_dumps as infinite recursions can occur
        return [];
    }

}
