<?php

namespace NGSOFT\Tools\Objects;

abstract class SimpleObject implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable {

    protected $storage = [];

    public function __construct(array $attributes = []) {
        if (count($attributes)) {
            $this->import($attributes);
        }
    }

    abstract public function import(array $attributes);

    abstract public function export(): array;

    /**
     * Merge SimpleObjects together
     * @param SimpleObject|array $array
     * @return $this
     */
    public function merge($array) {

        if ($array instanceof SimpleObject) {
            $array = $array->export();
        }
        if (is_array($array)) {
            $this->storage = array_merge($this->storage, $array);
        }
        return $this;
    }

    /**
     * Order by keys
     * @return $this
     */
    public function ksort(int $sort_flags = 0) {
        ksort($this->storage, $sort_flags);
        return $this;
    }

    /**
     * Reverse order by key
     * @param int $sort_flags
     * @return $this
     */
    public function krsort(int $sort_flags = 0) {

        krsort($this->storage, $sort_flags);
        return $this;
    }

    /**
     * Sorts the elements in place
     * @param callable $callback
     * @return $this
     */
    public function sort(callable $callback) {
        usort($this->storage, $callback);
        return $this;
    }

    /**
     * tests whether all elements in the array pass the test implemented by the provided function
     * @param callable $condition
     * @return bool
     */
    public function every(callable $condition): bool {
        if (!count($this)) {
            return false;
        }
        foreach ($this->storage as $k => $v) {
            if ($condition($v, $k) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Filters elements of an array using a callback function
     * @param callable $callback
     * @param int $flag
     * @return array
     */
    public function filter(callable $callback, int $flag = ARRAY_FILTER_USE_BOTH): array {
        return array_filter($this->storage, $callback, $flag);
    }

    /**
     * Pop the element off the end of array
     * @return any
     */
    public function pop() {
        return array_pop($this->storage);
    }

    /**
     * Push one or more elements onto the end of array
     * @param any $value
     * @return int
     */
    public function push($value) {
        return array_push($this->storage, $value);
    }

    /**
     * Shift an element off the beginning of array
     * @return any
     */
    public function shift() {
        return array_shift($this->storage);
    }

    /**
     * Prepend one element to the beginning of an array
     * @param any $value
     * @return int
     */
    public function unshift($value): int {
        return array_unshift($this->storage, $value);
    }

    /**
     * Applies the callback to the elements of the given arrays
     * @param \callable $callback
     * @return array
     */
    public function map(callable $callback, ...$args) {
        return array_map($callback, $this->storage, ...$args);
    }

    /**             Interfaces          * */
    public function count() {
        return count($this->storage);
    }

    public function offsetExists($k) {
        return array_key_exists($k, $this->storage);
    }

    public function offsetGet($k) {
        return $this->offsetExists($k) ? $this->storage[$k] : null;
    }

    public function offsetSet($k, $v) {
        if (is_null($k)) {
            $this->storage[] = $v;
            return;
        }
        $this->storage[$k] = $v;
    }

    public function offsetUnset($k) {
        if ($this->offsetExists($k)) {
            unset($this->storage[$k]);
        }
    }

    public function getIterator() {
        $array = $this->export();
        return new \ArrayIterator($array);
    }

    public function jsonSerialize() {
        return $this->export();
    }

    public function __toString() {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

}
