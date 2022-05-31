<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

class SimpleArray
{

    protected array $storage = [];

    public function unshift(mixed $value): int
    {
        if ($value instanceof self) $value = $value->storage;
        return array_unshift($this->storage, $value);
    }

    public function push(mixed $value): int
    {
        if ($value instanceof self) $value = $value->storage;
        return array_push($this->storage, $value);
    }

    public function shift()
    {
        $value = array_shift($this->storage);
        return is_array($value) ? new static($value) : $value;
    }

    public function pop(): mixed
    {
        $value = array_pop($this->storage);
        return is_array($value) ? new static($value) : $value;
    }

    /**
     * Returns the value index
     * @param mixed $value
     * @return int the index or -1 if not found
     */
    public function indexOf(mixed $value): int
    {
        if ($value instanceof self) {
            $value = $value->storage;
        }
        $id = array_search($value, $this->storage);
        return $id === false ? -1 : $id;
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
    public function map(callable $callback): self
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
    public function filter(callable $callback): self
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
    public function forEach(callable $callback): self
    {
        foreach ($this->getIterator() as $key => $value) $callback($value, $key, $this);
        return $this;
    }

}
