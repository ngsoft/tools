<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use ArrayAccess,
    Countable,
    Iterator,
    JsonSerializable;
use NGSOFT\Tools\Traits\{
    ArrayAccessCountable, ArrayAccessIterator
};
use Serializable;

class ArrayObject implements ArrayAccess, Countable, Iterator, JsonSerializable, Serializable {

    use ArrayAccessIterator,
        ArrayAccessCountable;

    /** @var array */
    protected $storage = [];

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
    public static function from(array &$array) {
        $obj = static::create();
        $obj->storage = &$array;
        return $obj;
    }

    /**
     * Exports Object to array
     * @return array
     */
    public function &toArray(): array {
        $value = &$this->storage;
        return $value;
    }

    /**
     * Creates an Object
     * @param array $array
     */
    public function __construct(array $array = []) {
        $this->storage = $array;
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        if ($offset === null) {
            $this->storage[] = [];
            $offset = array_key_last($this->storage);
        }
        if (isset($this->storage[$offset])) {
            if (is_array($this->storage[$offset])) {
                $array = &$this->storage[$offset];
                $result = clone $this;
                $result->storage = &$array;
                return $result;
            } else $result = &$this->storage[$offset];
        } else $result = null;
        return $result;
    }

    /** {@inheritdoc} */
    public function &__get($name) {
        $value = $this->offsetGet($name);
        return $value;
    }

    /** {@inheritdoc} */
    public function __isset($name) {
        return $this->offsetExists($name);
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $this->offsetSet($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset($name) {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return $this->storage;
    }

    public function serialize() {
        return serialize($this->storage);
    }

    public function unserialize($serialized) {
        $array = unserialize($serialized);
        if (is_array($array)) $this->storage = &$array;
    }

}
