<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use ArrayAccess,
    Countable,
    InvalidArgumentException,
    Iterator,
    JsonSerializable;
use NGSOFT\Tools\Traits\{
    ArrayAccessCountable, ArrayAccessIterator, ArrayAccessSpecials
};
use Serializable;

/**
 * Basic Array Like Object
 */
class SimpleObject implements ArrayAccess, Countable, Iterator, JsonSerializable, Serializable {

    use ArrayAccessIterator,
        ArrayAccessCountable,
        ArrayAccessSpecials;

    /** @var array */
    protected $storage = [];

    ////////////////////////////   Static Methods   ////////////////////////////

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
     * Instanciate a new instance using the given json
     * @param string $json
     * @return static
     * @throws InvalidArgumentException
     */
    public static function fromJson(string $json) {
        $array = json_decode($json, true);
        if (
                (json_last_error() === JSON_ERROR_NONE)
                and is_array($array)
        ) return static::from($array);
        throw new InvalidArgumentException("Cannot import: Invalid JSON");
    }

    /**
     * Instanciate a new instance using the given json file
     * @param string $filename
     * @return static
     * @throws InvalidArgumentException
     */
    public static function fromJsonFile(string $filename) {
        if (is_file($filename)) {
            $string = file_get_contents($filename);
            if (false !== $string) return static::fromJson($string);
        }
        throw new InvalidArgumentException("Cannot import: Invalid JSON File");
    }

    ////////////////////////////   Initialization   ////////////////////////////

    /**
     * Creates an Object
     * @param array $array
     */
    public function __construct(array $array = []) {
        $this->storage = $this->iterableToArray($array);
    }

    /** {@inheritdoc} */
    public function unserialize($serialized) {
        $array = \unserialize($serialized);
        if (is_array($array)) $this->storage = &$array;
    }

    /** {@inheritdoc} */
    public static function __set_state(array $array) {
        return static::from($array);
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
    public function serialize() {
        return \serialize($this->storage);
    }

    /** {@inheritdoc} */
    public function __toString() {
        return var_export($this->jsonSerialize(), true);
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return $this->storage;
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
