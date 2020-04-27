<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use ArrayAccess,
    Countable,
    InvalidArgumentException,
    Iterator,
    JsonSerializable,
    NGSOFT\Tools\Exceptions\RuntimeException,
    Serializable,
    stdClass;

/**
 * A Base Array Like Object
 */
class stdObject extends stdClass implements ArrayAccess, Countable, Iterator, Serializable, JsonSerializable {

    /** @var array */
    protected $storage = [];

    /**
     * Instanciate a new instance using the given array
     * @param array $array
     */
    public function __construct(array $array = []) {
        $this->import($array);
    }

    /**
     * Instanciate a new instance using the given array
     * @param array $array
     * @return static
     */
    public static function from(array $array) {
        $self = new static();
        $self->import($array);
        return $self;
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

    /**
     * Instanciate a new instance with an empty array
     * @return static
     */
    public static function create() {
        return new static();
    }

    ////////////////////////////   Utilities   ////////////////////////////

    /**
     * Import the given array by reference into the instance
     *
     * @param array $array
     */
    protected function import(array &$array) {
        $this->storage = $array;
    }

    /**
     * Normalize keys removing [_\-\.] from it
     * to access properties with camelCasedNames to replace camel-cased_names
     * also can use snake_cased to access snake.cased or snake-cased
     * @param string $prop
     * @return string
     */
    protected function getOriginalKey(string $prop): string {
        if (array_key_exists($prop, $this->storage)) return $prop;
        $nprop = strtolower($prop);
        foreach (array_keys($this->storage) as $key) {
            //for camelCased props
            $norm = strtolower(preg_replace('/[_\-\.]+(.)/', '\\1', $key));
            if ($norm === $nprop) return $key;
            //if access prop.key using prop_key
            $norm = strtolower(preg_replace('/[\-\.]/', '_', $key));
            if ($norm === $nprop) return $key;
        }
        return $prop;
    }

    ////////////////////////////   CacheAble   ////////////////////////////

    /**
     * Exports the Current contained Array
     * @return array
     */
    public function &toArray(): array {
        $value = &$this->storage;
        return $value;
    }

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return $this->toArray();
    }

    /** {@inheritdoc} */
    public function __toString() {
        return var_export($this->toArray(), true);
    }

    /** {@inheritdoc} */
    public static function __set_state(array $array) {
        return static::from($array);
    }

    /** {@inheritdoc} */
    public function serialize() {
        return serialize($this->storage);
    }

    /** {@inheritdoc} */
    public function unserialize($serialized) {
        $array = unserialize($serialized);
        $this->import($array);
    }

    ////////////////////////////   ArrayAccess   ////////////////////////////

    /** {@inheritdoc} */
    public function offsetExists($offset) {
        return isset($this->storage[$offset]);
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        if ($offset === null) {
            $this->storage[] = [];
            $offset = array_key_last($this->storage);
        }
        if (array_key_exists($offset, $this->storage)) {
            if (is_array($this->storage[$offset])) {
                $array = &$this->storage[$offset];
                $return = clone $this;
                $return->storage = &$array;
            } else $return = $this->storage[$offset];
            return $return;
        }
        throw new RuntimeException('Undefined index: ' . $offset, E_USER_NOTICE);
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if ($value instanceof self) $value = $value->toArray();
        if ($offset === null) $this->storage[] = $value;
        else $this->storage[$offset] = $value;
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset) {
        unset($this->storage[$offset]);
    }

    /** {@inheritdoc} */
    public function count() {
        return count($this->storage);
    }

    ////////////////////////////   PropertyAccess   ////////////////////////////

    /** {@inheritdoc} */
    public function __get($name) {
        return $this->offsetGet($this->getOriginalKey($name));
    }

    /** {@inheritdoc} */
    public function __isset($name) {
        return $this->offsetExists($this->getOriginalKey($name));
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $this->offsetSet($this->getOriginalKey($name), $value);
    }

    /** {@inheritdoc} */
    public function __unset($name) {
        $this->offsetUnset($this->getOriginalKey($name));
    }

    ////////////////////////////   Iterator   ////////////////////////////

    /** {@inheritdoc} */
    public function current() {
        $key = $this->key();
        if ($key === null) return false;
        return $this->offsetGet($key);
    }

    /** {@inheritdoc} */
    public function key() {
        return key($this->storage);
    }

    /** {@inheritdoc} */
    public function next() {
        next($this->storage);
    }

    /** {@inheritdoc} */
    public function rewind() {
        reset($this->storage);
    }

    /** {@inheritdoc} */
    public function valid() {
        return $this->key() !== null;
    }

}
