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

    public function __construct(
            array $array = [],
            protected bool $recursive = false
    )
    {
        $this->assertValidImport($array);
        $this->storage = $array;
    }

    /**
     * Appends a value at the end of the array updating the internal pointer
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    abstract protected function append(mixed $offset, mixed $value): void;

    /**
     * Checks if import is valid
     *
     * @param array $import
     * @return void
     */
    abstract protected function assertValidImport(array $import): void;

    protected function getNewInstance(): static
    {
        return new static(recursive: $this->recursive);
    }

    /**
     * Exports to json
     *
     * @param int $flags
     * @return string
     */
    public function toJson(int $flags = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    /**
     * Exports to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->storage;
    }

    /**
     * Clears the SimpleArray
     *
     * @return void
     */
    public function clear(): void
    {
        $array = [];
        $this->storage = &$array;
    }

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
        $result = $this->getNewInstance();
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
        $result = $this->getNewInstance();
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
            if ($this->recursive && is_array($value)) {
                $instance = $this->getNewInstance();
                $instance->storage = $value;
                $value = $instance;
            }
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

    /** {@inheritdoc} */
    public function __serialize(): array
    {

        return [$this->storage, $this->recursive];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data)
    {
        list($this->storage, $this->recursive) = $data;
    }

    public function __clone(): void
    {
        $storage = &$this->storage;
        foreach ($storage as $key => &$value) {
            if (is_object($value)) $value = clone $value;
        }
    }

}
