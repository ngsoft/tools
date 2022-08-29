<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    Generator,
    IteratorAggregate,
    JsonSerializable,
    LogicException,
    NGSOFT\Traits\StringableObject,
    Stringable,
    Traversable;
use function get_debug_type;

/**
 * The Map object holds key-value pairs and remembers the original insertion order of the keys.
 * Any value (both objects and primitive values) may be used as either a key or a value.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Map JS Map
 */
final class Map implements ArrayAccess, IteratorAggregate, Countable, Stringable, JsonSerializable
{

    use StringableObject;

    protected array $keys = [];
    protected array $values = [];

    protected function indexOf(mixed $key): int
    {
        $index = array_search($key, $this->keys, true);
        return $index !== false ? $index : -1;
    }

    protected function indexOfValue(mixed $value): int
    {
        $index = array_search($key, $this->values, true);
        return $index !== false ? $index : -1;
    }

    protected function append(mixed $key, mixed $value): static
    {
        $this->keys[] = $key;
        $this->values[] = $value;
        return $this;
    }

    protected function getIndexes(Sort $sort = Sort::ASC): Generator
    {
        $keys = array_keys($this->keys);
        if ($sort->is(Sort::DESC)) {
            $keys = array_reverse($keys);
        }
        foreach ($keys as $offset) { yield $offset; }
    }

    /**
     * The clear() method removes all elements from a Map object.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->keys = $this->values = [];
    }

    /**
     * The delete() method removes the specified element from a Map object by key.
     *
     * @param mixed $key
     * @return bool
     */
    public function delete(mixed $key): bool
    {
        if (($index = $this->indexOf($key)) > -1) {
            unset($this->keys[$index], $this->values[$index]);
            return true;
        }
        return false;
    }

    /**
     * The get() method returns a specified element from a Map object.
     * If the value that is associated to the provided key is an object,
     * then you will get a reference to that object and any change made
     * to that object will effectively modify it inside the Map object.
     *
     * @param mixed $key
     * @return mixed
     */
    public function get(mixed $key): mixed
    {
        return
                ($index = $this->indexOf($key)) > -1 ?
                $this->values[$index] :
                null;
    }

    /**
     * The set() method adds or updates an element with a specified key and a value to a Map object.
     */
    public function set(mixed $key, mixed $value): static
    {
        $this->delete($key);
        return $this->append($key, $value);
    }

    /**
     * The add() method adds an element with a specified key and a value to a Map object if it does'n already exists.
     */
    public function add(mixed $key, mixed $value): static
    {

        if ($this->has($key)) {
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
     * The keys() method returns a new iterator object that contains the keys for each element in the Map object in insertion order
     */
    public function keys(Sort $sort = Sort::ASC): iterable
    {
        $keys = $sort->is(Sort::ASC) ? $this->keys : array_reverse($this->keys);
        foreach ($keys as $key) { yield $key; }
    }

    /**
     * The values() method returns a new iterator object that contains the values for each element in the Map object in insertion order.
     */
    public function values(Sort $sort = Sort::ASC): iterable
    {
        $values = $sort->is(Sort::ASC) ? $this->values : array_reverse($this->values);
        foreach ($values as $value) { yield $value; }
    }

    /**
     * The entries() method returns a new iterator object that contains the [key, value] pairs for each element in the Map object in insertion order.
     */
    public function entries(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->getIndexes($sort) as $offset) { yield $this->keys[$offset] => $this->values[$offset]; }
    }

    /**
     * The forEach() method executes a provided function once per each key/value pair in the Map object, in insertion order.
     */
    public function forEach(callable $callable): void
    {
        foreach ($this->entries() as $key => $value) { $callable($value, $key, $this); }
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        $this->delete($offset);
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->keys);
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        yield from $this->entries();
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return [];
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        $result = [];

        foreach ($this->entries() as $key => $value) {
            $result[is_scalar($key) ? $key : sprintf('%s#%d', get_debug_type($key), spl_object_id($key))] = $value;
        }

        return $result;
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->keys, $this->values];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {
        list($this->keys, $this->values) = $data;
    }

    /** {@inheritdoc} */
    public function __clone(): void
    {

    }

}
