<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Traits;

use ArrayIterator;
use BadMethodCallException;
use function NGSOFT\Tools\array_flatten;

trait JSArrayMethods {

    /** @var array */
    protected $storage;

    /**
     * @param iterable $input
     */
    public function __construct(iterable $input = []) {
        $this->loadArray((array) $input);
    }

    /**
     * Loads the array
     */
    abstract protected function loadArray(array $array);

    /** {@inheritdoc} */
    public function length(): int {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    public function __toString() {
        return var_export($this->storage, true);
    }

    ////////////////////////////   From \ArrayObject   ////////////////////////////

    /**
     * Appends the value
     * @param mixed $value
     * @return void
     */
    public function append($value): void {
        $this->push($value);
    }

    /**
     * Sort the entries by key
     * @return void
     */
    public function ksort(): void {
        ksort($this->storage);
    }

    /**
     * Sort an array using a case insensitive "natural order" algorithm
     * @return void
     */
    public function natcasesort(): void {
        natcasesort($this->storage);
    }

    /**
     * Sort entries using a "natural order" algorithm
     * @return void
     */
    public function natsort(): void {
        natsort($this->storage);
    }

    /**
     * Sort the entries with a user-defined comparison function and maintain key association
     * @param callable $callback
     * @return void
     */
    public function uasort(callable $callback): void {
        $this->sort($callback);
    }

    /**
     * Sort the entries by keys using a user-defined comparison function
     * @param callable $callback
     * @return void
     */
    public function uksort(callable $callback): void {
        uksort($this->storage, $callback);
    }

    ////////////////////////////   Static   ////////////////////////////

    /**
     * {@inheritdoc}
     */
    public static function From($value, callable $mapFn = null) {
        assert(is_iterable($value));
        $array = (array) $value;
        if (is_callable($mapFn)) $array = array_map($mapFn, $array);
        return new static($array);
    }

    /** {@inheritdoc} */
    public static function isArray($value): bool {
        return is_array($value);
    }

    /** {@inheritdoc} */
    public static function isIterable($value): bool {
        return is_iterable($value);
    }

    /**
     * {@inheritdoc}
     */
    public static function of(...$values) {
        return new static($values);
    }

    ////////////////////////////   With Exceptions   ////////////////////////////

    /** {@inheritdoc} */
    public function fill($value, int $num, int $start = 0) {
        $this->hasNonNumericKeys($this->storage, __METHOD__);
        $newarr = $this->storage;
        for ($i = $start; $i < ($start + $num); ++$i) {
            $newarr[$i] = $value;
        }
        ksort($newarr, SORT_NUMERIC);
        return new static($newarr);
    }

    /** {@inheritdoc} */
    public function splice(int $start, ...$args) {
        $this->hasNonNumericKeys($this->storage, __METHOD__);
        array_splice($this->storage, $start, ...$args);
        return $this;
    }

    /** {@inheritdoc} */
    public function slice(int $start = 0, int $length = null) {
        $this->hasNonNumericKeys($this->storage, __METHOD__);
        return new static(array_slice($this->storage, $start, $length));
    }

    /**
     * Checks recursively if array has non numeric keys
     * @param array $array
     * @param string $method
     * @throws BadMethodCallException
     */
    protected function hasNonNumericKeys(array $array, string $method) {

        array_map(function ($key) use ($method) {
            if (!is_int($key)) throw new BadMethodCallException("Array has non numeric keys, cannot call that method, $method.");
        }, array_keys($array));
    }

    ////////////////////////////   Instance   ////////////////////////////

    /** {@inheritdoc} */
    public function flat(int $depth = 1) {
        $new = array_flatten($this->storage, $depth);
        return new static($new);
    }

    /** {@inheritdoc} */
    public function flatMap(callable $callback) {
        $new = [];
        foreach ($this->storage as $k => $v) {
            $new[$k] = $callback($v, $k);
        }
        $new = array_flatten($new, 1);
        return new static($new);
    }

    /** {@inheritdoc} */
    public function sort(callable $callback) {
        uasort($this->storage, $callback);
        return $this;
    }

    /** {@inheritdoc} */
    public function reverse() {
        $this->storage = array_reverse($this->storage);
        return $this;
    }

    /** {@inheritdoc} */
    public function pop() {
        return array_pop($this->storage);
    }

    /** {@inheritdoc} */
    public function push(...$values): int {
        return array_push($this->storage, ...$values);
    }

    /** {@inheritdoc} */
    public function shift() {
        return array_shift($this->storage);
    }

    /** {@inheritdoc} */
    public function unshift(...$values): int {
        return array_unshift($this->storage, ...$values);
    }

    /** {@inheritdoc} */
    public function reduce(callable $callback, $initial = null) {
        return array_reduce($this->storage, $callback, $initial);
    }

    /** {@inheritdoc} */
    public function reduceRight(callable $callback, $initial = null) {
        return array_reduce(array_reverse($this->storage), $callback, $initial);
    }

    /** {@inheritdoc} */
    public function toString(): string {
        return $this->__toString();
    }

    /** {@inheritdoc} */
    public function find(callable $callback) {
        foreach ($this->storage as $k => $v) {
            if ($callback($v, $k) === true) return $v;
        }
        return false;
    }

    /** {@inheritdoc} */
    public function findIndex(callable $callback) {
        foreach ($this->storage as $k => $v) {
            if ($callback($v, $k) === true) return $k;
        }
        return false;
    }

    /** {@inheritdoc} */
    public function indexOf($value) {
        return array_search($value, $this->storage, true);
    }

    /** {@inheritdoc} */
    public function lastIndexOf($value) {
        $rev = array_reverse($this->storage, true);
        return array_search($value, $rev, true);
    }

    /** {@inheritdoc} */
    public function concat(iterable ...$values) {
        $merged = array_merge([], $this->storage);
        foreach ($values as $value) {
            if (!is_array($value)) $value = [$value];
            $merged = array_merge($merged, $value);
        }
        return new static($merged);
    }

    /** {@inheritdoc} */
    public function entries(): iterable {
        return new ArrayIterator($this->storage);
    }

    /** {@inheritdoc} */
    public function filter(callable $callback) {
        return new static(array_filter($this->storage, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /** {@inheritdoc} */
    public function includes($value): bool {
        return in_array($value, $this->storage, true);
    }

    /** {@inheritdoc} */
    public function join(string $glue): string {
        return implode($glue, $this->storage);
    }

    /** {@inheritdoc} */
    public function keys(): iterable {
        return array_keys($this->storage);
    }

    /** {@inheritdoc} */
    public function values(): iterable {
        return array_values($this->storage);
    }

    /** {@inheritdoc} */
    public function map(callable $callback) {
        //array_map does not contains the required args
        $new = [];
        foreach ($this->storage as $k => $v) {
            $new[$k] = $callback($v, $k);
        }
        return new static($new);
    }

    /** {@inheritdoc} */
    public function forEach(callable $callback): void {
        foreach ($this->storage as $k => $v) {
            $callback($v, $k);
        }
    }

    /** {@inheritdoc} */
    public function every(callable $callback): bool {
        foreach ($this->storage as $k => $v) {
            if (!$callback($v, $k)) return false;
        }
        return true;
    }

    /** {@inheritdoc} */
    public function some(callable $callback): bool {
        foreach ($this->storage as $k => $v) {
            if (!$callback($v, $k)) return false;
        }
        return true;
    }

}
