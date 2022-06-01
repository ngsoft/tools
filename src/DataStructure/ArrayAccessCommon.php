<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    Generator,
    IteratorAggregate,
    JsonSerializable,
    Stringable,
    Traversable;

abstract class ArrayAccessCommon implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Stringable
{

    protected array $storage;

    /**
     * Appens a value at the end of the array updating the internal pointer
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    abstract protected function append(mixed $offset, mixed $value): void;

    /**
     * Returns a new iterator indexed by id
     *
     * @return Generator
     */
    public function entries(): Generator
    {
        yield from $this->getIterator();
    }

    /**
     * Returns a new iterator with only the values
     * @return Generator
     */
    public function values(): Generator
    {
        foreach ($this->getIterator() as $value) { yield $value; }
    }

    /**
     * Returns a new iterator with only the indexes
     * @return Generator
     */
    public function keys(): Generator
    {
        foreach (array_keys($this->storage) as $index) { yield $index; }
    }

    /**
     * Applies the callback to the elements of the storage and returns a copy
     *
     * @param callable $callback a callback
     * @return static
     */
    public function map(callable $callback): static
    {
        $result = new static();
        foreach ($this->getIterator() as $key => $value) { $result->offsetSet($key, $callback($value, $key, $this)); }
        return $result;
    }

    /**
     * Returns a copy with all the elements that passes the test
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $result = new static();
        foreach ($this->getIterator() as $key => $value) {
            $retval = $callback($value, $key, $this);
            if (!$retval) continue;
            $result->offsetSet($key, $value);
        }
        return $result;
    }

    /**
     * Tests if at least one of the elements from the storage pass the test implemented by the callable
     *
     * @param callable $callback
     * @return boolean
     */
    public function some(callable $callback): bool
    {
        if ($this->count() === 0) return false;
        foreach ($this->getIterator() as $key => $value) {
            $retval = $callback($value, $key, $this);
            if (!$retval) continue;
            return true;
        }
        return false;
    }

    /**
     * Tests if all the elements from the storage pass the test implemented by the callable
     *
     * @param callable $callback
     * @return boolean
     */
    public function every(callable $callback): bool
    {
        if ($this->count() === 0) return false;
        foreach ($this->getIterator() as $key => $value) {
            $retval = $callback($value, $key, $this);
            if (!$retval) return false;
        }
        return true;
    }

    /**
     * Runs the given callable for each of the elements
     * @param callable $callback
     * @return static
     */
    public function forEach(callable $callback): static
    {
        foreach ($this->getIterator() as $key => $value) $callback($value, $key, $this);
        return $this;
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->storage);
    }

    /** {@inheritdoc} */
    public function &offsetGet(mixed $offset): mixed
    {
        $value = null;
        if (null === $offset) {
            $this->storage[] = [];
            $offset = array_key_last($this->storage);
        }
        if ($this->offsetExists($offset)) {
            $value = &$this->storage[$offset];
            if ($this->recursive && is_array($value)) $value = new static($value, $this->recursive);
        }
        return $value;
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->append($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$offset]);
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        $keys = array_keys($this->storage);
        foreach ($keys as $offset) {
            $value = $this->offsetGet($offset);
            yield $offset => $value;
        }
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return $this->storage;
    }

    /** {@inheritdoc} */
    public function __toString(): string
    {
        return sprintf('[object %s]', static::class);
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return $this->storage;
    }

}
