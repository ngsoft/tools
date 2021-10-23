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
        $this->storage[Resolver::class] = new Resolver($this);
        $this->resolver = &$this->storage[Resolver::class];
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
        if (!isset($this->storage[$id])) {

            if (
                    class_exists($id) and
                    $resolved = $this->resolver->resolveClassName($id)
            ) $this->storage[$id] = $resolved;
            else throw new NotFoundException($id, $this);
        } elseif (// first call to $id that is a definition
                is_callable($this->storage[$id]) and
                // can disrupt container as a class with __invoke method that is not a Closure is callable
                !(is_object($this->storage[$id]) && !($this->storage[$id] instanceof Closure))
        ) {
            if ($resolved = $this->resolver->resolveCallable($this->storage[$id])) $this->storage[$id] = $resolved;
            else throw new NotFoundException($id, $this);
        }
        return $this->storage[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id) {
        return
                isset($this->storage[$id]) or
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
                return !$reflector->isAbstract();
            } catch (ReflectionException $error) {

            }
        }

        return false;
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        // do not bloat var_dumps as infinite recursions can occur
        return [];
    }

}
