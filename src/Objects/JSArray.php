<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use ArrayIterator;
use IteratorAggregate;
use NGSOFT\Tools\Interfaces\CacheAble;
use NGSOFT\Tools\Interfaces\JSArrayInterface;
use NGSOFT\Tools\Traits\JSArrayMethods;

/**
 * A library that reproduces the Javascript Array Object for PHP
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array
 */
class JSArray implements IteratorAggregate, CacheAble, JSArrayInterface {

    use JSArrayMethods;

    /** {@inheritdoc} */
    protected function loadArray(array $array) {
        $this->storage = $array;
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
    public function unserialize($serialized) {
        $this->storage = unserialize($serialized);
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return json_encode($this->storage);
    }

    /** {@inheritdoc} */
    public static function __set_state($array) {
        return new static($array);
    }

    ////////////////////////////   Cacheable   ////////////////////////////

    /** {@inheritdoc} */
    public static function createFromArray(array $data) {
        return new static($data);
    }

    /** {@inheritdoc} */
    public function toArray(): array {
        return $this->storage;
    }

}
