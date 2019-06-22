<?php

namespace NGSOFT\Tools\Objects;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use NGSOFT\Tools\Interfaces\CacheAble;
use NGSOFT\Tools\Interfaces\JSArrayInterface;
use NGSOFT\Tools\Traits\JSArrayMethods;
use Serializable;

/**
 * A library that reproduces the Javascript Array Object for PHP
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array
 */
class JSArray implements IteratorAggregate, ArrayAccess, Serializable, Countable, JsonSerializable, CacheAble, JSArrayInterface {

    use JSArrayMethods;

    /** @var array */
    protected $storage;

    /** @param iterable $input */
    public function __construct(iterable $input = []) {
        parent::__construct($input);
    }

    /** {@inheritdoc} */
    public function toArray(): array {
        return $this->getArrayCopy();
    }

    /** {@inheritdoc} */
    public function length(): int {
        return count($this);
    }

    ////////////////////////////   ArrayAccess ...   ////////////////////////////

    /** {@inheritdoc} */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->storage);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset) {
        return $this->storage[$offset] ?? null;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if ($offset === null) $this->storage[] = $value;
        else $this->storage[$offset] = $value;
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset) {
        unset($this->storage[$offset]);
    }

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    public function getIterator() {
        return new ArrayIterator($this->storage);
    }

    /** {@inheritdoc} */
    public function serialize() {
        return serialize($this->storage);
    }

    /** {@inheritdoc} */
    public function unserialize(string $serialized): void {
        $this->storage = unserialize($serialized);
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return json_encode($this->storage);
    }

    /** {@inheritdoc} */
    public function __toString() {
        return var_export($this->storage, true);
    }

    /** {@inheritdoc} */
    public static function __set_state($array) {
        return new static($array);
    }

    ////////////////////////////   Cacheable   ////////////////////////////

    /** {@inheritdoc} */
    public static function createFromArray(array $data): self {
        return new static($data);
    }

    ////////////////////////////   Static   ////////////////////////////

    /** {@inheritdoc} */
    public static function From($value, callable $mapFn = null): JSArrayInterface {
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

    /** {@inheritdoc} */
    public static function of(...$values): JSArrayInterface {
        return new static($values);
    }

    ////////////////////////////   Instance   ////////////////////////////

    /** {@inheritdoc} */
    public function reverse(): self {
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
            if ($find($v, $k) === true) return $v;
        }
        return false;
    }

    /** {@inheritdoc} */
    public function findIndex(callable $callback) {
        foreach ($this->storage as $k => $v) {
            if ($find($v, $k) === true) return $k;
        }
        return false;
    }

    /** {@inheritdoc} */
    public function indexOf($value) {
        return array_search($value, $this->storage, true);
    }

    /** {@inheritdoc} */
    public function lastIndexOf($value) {
        $rev = array_reverse($array, true);
        return array_search($value, $rev, true);
    }

    /** {@inheritdoc} */
    public function concat(...$values): JSArrayInterface {
        $merged = array_merge([], $this->storage);
        foreach ($values as $value) {
            if (!is_array($value)) $value = [$value];
            $merged = array_merge($merged, $value);
        }
        return new static($merged);
    }

    /** {@inheritdoc} */
    public function entries(): iterable {
        return $this->getIterator();
    }

    /** {@inheritdoc} */
    public function filter(callable $callback): JSArrayInterface {
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
    public function map(callable $callback): JSArrayInterface {
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
    public function every(callable $callback): JSArrayInterface {
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
