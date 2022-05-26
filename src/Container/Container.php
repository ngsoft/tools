<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;
use NGSOFT\Exceptions\{
    InvalidDefinition, NotFoundException
};
use Psr\Container\ContainerInterface,
    ReflectionClass,
    ReflectionException;

class_exists(NotFoundException::class);
class_exists(InvalidDefinition::class);

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
        $this->storage[ContainerInterface::class] = $this->storage[Container::class] = $this;
        $this->addDefinitions($definitions);
    }

    /**
     * Add an Entry/Definition to the container
     *
     * @param string $id
     * @param mixed $value
     * @return static
     */
    public function set(string $id, mixed $value): static {
        return $this->addDefinitions([$id => $value]);
    }

    /**
     *
     * @param array $definitions
     * @return static
     * @throws InvalidDefinition
     */
    public function addDefinitions(array $definitions): static {

        foreach ($definitions as $id => $value) {
            if (!is_string($id)) {
                throw new InvalidDefinition($this, $id, $value);
            }
            if ($this->isDefined($id)) continue;
            if ($this->isCallable($value)) {
                $this->definitions[$id] = $value;
            } else $this->storage[$id] = $value;
        }

        return $this;
    }

    /** {@inheritdoc} */
    public function get(string $id) {
        $resolved = null;
        if ($this->has($id)) {
            if ($this->isDefined($id)) return $this->storage[$id];
            elseif ($this->hasDefinition($id)) $resolved = $this->resolver->resolveCallable($this->definitions[$id]);
            else $resolved = $this->resolver->resolveClassName($id);
        }
        if (null === $resolved) throw new NotFoundException($this, $id);
        return $this->storage[$id] = $resolved;
    }

    /** {@inheritdoc} */
    public function has(string $id): bool {

        return
                !is_null($this->storage[$id] ?? $this->definitions[$id] ?? null) ||
                $this->isValidClass($id);
    }

    /**
     * Checks if definition is defined (or already resolved)
     *
     * @param string $id
     * @return bool
     */
    private function isDefined(string $id): bool {
        return !is_null($this->storage[$id]);
    }

    /**
     * Checks if a definition exists for entry
     *
     * @param string $id
     * @return bool
     */
    private function hasDefinition(string $id): bool {
        return $this->isCallable($this->definitions[$id] ?? null);
    }

    /**
     * Checks if class is valid
     * @param string $className
     * @return bool
     */
    private function isValidClass(string $className): bool {

        static $cache = [];
        if (is_bool($cache[$className] ?? null)) return $cache[$className];
        try {
            $reflector = new ReflectionClass($className);
            $cache[$className] = $reflector->isInstantiable();
        } catch (ReflectionException) {
            $cache[$className] = false;
        }
        return $cache[$className];
    }

    private function isCallable(mixed $input): bool {

        return
                is_callable($input) &&
                !(is_object($input) && !($input instanceof Closure));
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        // do not bloat var_dumps as infinite recursions can occur

        $defined = [];
        foreach (array_keys($this->definitions) as $id) $defined[$id] = get_debug_type($this->definitions[$id]);
        foreach (array_keys($this->storage) as $id) $defined[$id] = get_debug_type($this->storage[$id]);
        return $defined;
    }

}
