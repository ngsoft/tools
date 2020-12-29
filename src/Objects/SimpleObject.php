<?php

namespace NGSOFT\Tools\Objects;

use ArrayAccess,
    Countable,
    Iterator,
    JsonSerializable;
use NGSOFT\Tools\Traits\{
    ArrayAccessCountable, ArrayAccessIterator
};
use Serializable;

class SimpleObject implements ArrayAccess, Countable, Iterator, JsonSerializable, Serializable {

    use ArrayAccessIterator,
        ArrayAccessCountable;

    /** @var array */
    protected $storage = [];

    ////////////////////////////   Initialization   ////////////////////////////

    /**
     * Creates a new Object
     * @return static
     */
    public static function create() {
        return new static();
    }

    /**
     * Creates an Objec from an array
     * @param array $array
     * @return static
     */
    public static function from(array $array) {
        $obj = static::create();
        $obj->storage = $array;
        return $obj;
    }

    /**
     * Creates an Object
     * @param array $array
     */
    public function __construct(array $array = []) {
        $this->storage = $array;
    }

    /** {@inheritdoc} */
    public function unserialize(string $serialized) {
        $array = \unserialize($serialized);
        if (is_array($array)) $this->storage = &$array;
    }

    ////////////////////////////   Export   ////////////////////////////

    /**
     * Exports Object to array
     * @return array
     */
    public function &toArray(): array {
        $value = &$this->storage;
        return $value;
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return $this->storage;
    }

    /** {@inheritdoc} */
    public function serialize() {
        return \serialize($this->storage);
    }

    ////////////////////////////   Getters/Setters   ////////////////////////////

    /** {@inheritdoc} */
    public function &__get($name) {
        $value = $this->offsetGet($name);
        return $value;
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $this->offsetSet($name, $value);
    }

    ////////////////////////////   Exists/Unset   ////////////////////////////

    /** {@inheritdoc} */
    public function __unset($name) {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function __isset($name) {
        return $this->offsetExists($name);
    }

}
