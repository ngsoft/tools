<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use ArrayAccess,
    Generator,
    LogicException,
    UnexpectedValueException;

trait ArrayAccessEssentials {

    use ArrayAccessCountable;

    /** @var array */
    protected $storage = [];

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * array_replace_recursive that appends numeric keys instead of replacing them
     * @param array $orig
     * @param array $array
     * @return array
     */
    protected function merge(array $orig, array $array): array {
        $result = $orig;
        foreach ($array as $key => $value) {
            if (is_int($key)) $result[] = $value;
            elseif (
                    is_array($value)
                    and isset($result[$key])
                    and is_array($result[$key])
            ) {
                $result[$key] = $this->merge($result[$key], $value);
            } else $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Convert iterable to array
     * @param iterable $obj
     * @return array
     */
    protected function iterableToArray(iterable $obj): array {
        $result = [];
        foreach ($obj as $key => $value) {
            if (
                    is_iterable($value)
                    and ($value instanceof self or is_array($value))
            ) {
                $result[$key] = $this->iterableToArray($value);
            } else $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Checks if trait is used in a ArrayAccess class
     * @throws LogicException
     */
    protected function assertArrayAccess() {
        if (!($this instanceof ArrayAccess)) {
            throw new LogicException(sprintf('%s is not an instance of ArrayAccess.', get_class($this)));
        }
    }

    /**
     * Checks if value is boolean (callables return values)
     * @param mixed $value
     * @throws UnexpectedValueException
     */
    protected function assertIsBool($value) {
        if (!is_bool($value)) {
            throw new UnexpectedValueException(sprintf('Invalid return value: boolean requested, %s given.', gettype($value)));
        }
    }

    ////////////////////////////   API   ////////////////////////////

    /**
     * Checks if a value exists in the storage
     * @param mixed $value
     * @return bool
     */
    public function has($value): bool {
        if ($value instanceof self) {
            $value = $value->storage;
        }
        return in_array($value, $this->storage);
    }

    /**
     * Returns the value index
     * @param mixed $value
     * @return int the index or -1 if not found
     */
    public function indexOf($value): int {
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
    public function entries(): Generator {
        foreach ($this->getIterator() as $id => $value) {
            yield $id => $value;
        }
    }

    /**
     * Returns a new iterator with only the values
     * @return Generator
     */
    public function values(): Generator {
        foreach ($this->getIterator() as $value) {
            yield $value;
        }
    }

    /**
     * Returns a new iterator with only the indexes
     * @return Generator
     */
    public function keys(): Generator {
        foreach (array_keys($this->storage) as $index) {
            yield $index;
        }
    }

    /**
     * Concat multiples iterables with the current storage and returns a copy
     *
     * @param iterable ...$objects
     * @return static
     */
    public function concat(iterable ...$objects): self {
        $result = clone $this;
        $result->clear();
        foreach ($this->storage as $key => $value) {
            $result->storage[$key] = $value;
        }
        foreach ($objects as $obj) {
            $array = $this->iterableToArray($obj);
            $result->storage = $result->merge($result->storage, $array);
        }
        return $result;
    }

    /**
     * Applies the callback to the elements of the storage and returns a copy
     *
     * @param callable $callback a callback
     * @return static
     */
    public function map(callable $callback): self {
        //we don't know the constructor arguments
        $result = clone $this;
        $result->clear();
        foreach ($this->getIterator() as $key => $value) {
            $result->offsetSet($key, $callback($value, $key));
        }
        return $result;
    }

    /**
     * Returns a copy with all the elements that passes the test
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): self {
        $result = clone $this;
        $result->clear();
        foreach ($this->getIterator() as $key => $value) {
            $retval = $callback($value, $key);
            $this->assertIsBool($retval);
            if ($retval === true) $result->offsetSet($key, $value);
        }
        return $result;
    }

    /**
     * Tests if at least one of the elements from the storage pass the test implemented by the callable
     *
     * @param callable $callback
     * @return boolean
     */
    public function some(callable $callback): bool {
        if ($this->count() === 0) return false;
        foreach ($this->getIterator() as $key => $value) {
            $retval = $callback($value, $key);
            $this->assertIsBool($retval);
            if (true === $retval) return true;
        }
        return false;
    }

    /**
     * Tests if all the elements from the storage pass the test implemented by the callable
     * 
     * @param callable $callback
     * @return boolean
     */
    public function every(callable $callback): bool {
        if ($this->count() === 0) return false;
        foreach ($this->getIterator() as $key => $value) {
            $retval = $callback($value, $key);
            $this->assertIsBool($retval);
            if (false === $retval) return false;
        }
        return true;
    }

    /**
     * Runs the given callable for each of the elements
     * @param callable $callback
     * @return static
     */
    public function forEach(callable $callback): self {
        foreach ($this->getIterator() as $key => $value) $callback($value, $key);
        return $this;
    }

}
