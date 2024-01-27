<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Traits\ObjectLock;
use NGSOFT\Traits\ReversibleIteratorTrait;
use NGSOFT\Traits\StringableObject;

/**
 * The Map object holds key-value pairs and remembers the original insertion order of the keys.
 * Any value (both objects and primitive values) may be used as either a key or a value.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Map JS Map
 */
final class Map implements \ArrayAccess, ReversibleIterator, \Stringable, \JsonSerializable
{
    use StringableObject;

    use CommonMethods;

    use ObjectLock;

    use ReversibleIteratorTrait;

    protected array $keys   = [];
    protected array $values = [];

    public function __construct(?iterable $iterable = null)
    {
        if (is_iterable($iterable))
        {
            $this->importIterable($iterable);
        }
    }

    public function __debugInfo(): array
    {
        $result = [];

        foreach ($this->entries() as $key => $value)
        {
            $result[is_scalar($key) ? $key : sprintf('%s#%d', \get_debug_type($key), spl_object_id($key))] = $value;
        }

        return $result;
    }

    public function __serialize(): array
    {
        return [$this->keys, $this->values, $this->locked];
    }

    public function __unserialize(array $data): void
    {
        list($this->keys, $this->values, $this->locked) = $data;
    }

    public function __clone(): void
    {
        $this->keys   = $this->cloneArray($this->keys);
        $this->values = $this->cloneArray($this->values);
    }

    /**
     * Creates a new instance initialized with an iterable.
     */
    public static function of(iterable $iterable): self
    {
        return new self($iterable);
    }

    /**
     * Creates a new instance.
     */
    public static function new(?iterable $iterable = null): self
    {
        return new self($iterable);
    }

    /**
     * Create a new Map.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * The clear() method removes all elements from a Map object.
     */
    public function clear(): void
    {
        if ( ! $this->isLocked())
        {
            $this->keys = $this->values = [];
        }
    }

    /**
     * The delete() method removes the specified element from a Map object by key.
     */
    public function delete(mixed $key): bool
    {
        if ( ! $this->isLocked())
        {
            if (($index = $this->indexOf($key)) > -1)
            {
                unset($this->keys[$index], $this->values[$index]);
                return true;
            }
        }

        return false;
    }

    /**
     * The get() method returns a specified element from a Map object.
     * If the value that is associated to the provided key is an object,
     * then you will get a reference to that object and any change made
     * to that object will effectively modify it inside the Map object.
     */
    public function get(mixed $key): mixed
    {
        return
            ($index = $this->indexOf($key)) > -1 ?
                $this->values[$index] :
                null;
    }

    /**
     * The search() method returns the first key match from a value.
     */
    public function search(mixed $value): mixed
    {
        return
            ($index = $this->indexOfValue($value)) > -1 ?
                $this->keys[$index] :
                null;
    }

    /**
     * The set() method adds or updates an element with a specified key and a value to a Map object.
     */
    public function set(mixed $key, mixed $value): self
    {
        $this->delete($key);
        return $this->append($key, $value);
    }

    /**
     * The add() method adds an element with a specified key and a value to a Map object if it doesn't already exist.
     */
    public function add(mixed $key, mixed $value): self
    {
        if ($this->has($key))
        {
            return $this;
        }
        return $this->append($key, $value);
    }

    /**
     * The has() method returns a boolean indicating whether an element with the specified key exists or not.
     */
    public function has(mixed $key): bool
    {
        return $this->indexOf($key) > -1;
    }

    /**
     * The keys() method returns a new iterator object that contains the keys for each element in the Map object in insertion order.
     */
    public function keys(Sort $sort = Sort::ASC): iterable
    {
        yield from $this->sortArray($this->keys, $sort);
    }

    /**
     * The values() method returns a new iterator object that contains the values for each element in the Map object in insertion order.
     */
    public function values(Sort $sort = Sort::ASC): iterable
    {
        yield from $this->sortArray($this->values, $sort);
    }

    /**
     * The entries() method returns a new iterator object that contains the [key, value] pairs for each element in the Map object in insertion order.
     */
    public function entries(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->sortArray(array_keys($this->keys), $sort) as $offset)
        {
            yield $this->keys[$offset] => $this->values[$offset];
        }
    }

    /**
     * The forEach() method executes a provided function once per each key/value pair in the Map object, in insertion order.
     */
    public function forEach(callable $callable): void
    {
        foreach ($this->entries() as $key => $value)
        {
            $callable($value, $key, $this);
        }
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
        $this->delete($offset);
    }

    public function count(): int
    {
        return count($this->keys);
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->entries() as $key => $value)
        {
            $result[] = [$key, $value];
        }

        return $result;
    }

    protected function indexOf(mixed $key): int
    {
        $index = array_search($key, $this->keys, true);
        return false !== $index ? $index : -1;
    }

    protected function indexOfValue(mixed $value): int
    {
        $index = array_search($value, $this->values, true);
        return false !== $index ? $index : -1;
    }

    protected function append(mixed $key, mixed $value): self
    {
        if ( ! $this->isLocked())
        {
            $this->keys[]   = $key;
            $this->values[] = $value;
        }

        return $this;
    }

    private function importIterable(iterable $iterable): void
    {
        foreach ($iterable as $item)
        {
            if ( ! is_list($item) || 2 < count($item))
            {
                continue;
            }

            $this->set($item[0], $item[1]);
        }
    }
}
