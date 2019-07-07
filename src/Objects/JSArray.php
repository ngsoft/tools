<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use IteratorAggregate;
use NGSOFT\Tools\Interfaces\CacheAble;
use NGSOFT\Tools\Interfaces\JSArrayInterface;
use NGSOFT\Tools\Traits\ArrayAccessTrait;
use NGSOFT\Tools\Traits\JSArrayMethods;

/**
 * A library that reproduces the Javascript Array Object for PHP
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array
 */
class JSArray implements IteratorAggregate, CacheAble, JSArrayInterface {

    use JSArrayMethods;
    use ArrayAccessTrait;

    /** {@inheritdoc} */
    protected function loadArray(array $array) {
        $this->storage = $array;
    }

    ////////////////////////////   ArrayAccess ...   ////////////////////////////

    /** {@inheritdoc} */
    public function serialize() {
        return serialize($this->toArray());
    }

    /** {@inheritdoc} */
    public function unserialize($serialized) {
        $this->loadArray(unserialize($serialized));
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return json_encode($this->toArray());
    }

    ////////////////////////////   Cacheable   ////////////////////////////

    /** {@inheritdoc} */
    public static function __set_state($array) {
        return new static($array);
    }

    /** {@inheritdoc} */
    public function toArray(): array {
        return $this->storage;
    }

}
