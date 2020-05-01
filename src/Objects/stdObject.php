<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use ArrayAccess,
    Countable,
    InvalidArgumentException,
    Iterator,
    JsonSerializable;
use NGSOFT\Tools\{
    Exceptions\RuntimeException, Traits\ArrayAccessIteratorTrait
};
use Serializable,
    stdClass;

/**
 * A Base Array Like Object
 */
class stdObject extends stdClass implements ArrayAccess, Countable, Iterator, Serializable, JsonSerializable {

    use ArrayAccessIteratorTrait;

    /** @var bool */
    public static $debug = false;

    /** @var array */
    protected $storage = [];

    ////////////////////////////   Initialization   ////////////////////////////

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

    ////////////////////////////   JS Like Methods   ////////////////////////////

    /**
     * Makes an array_replace_recursive() on the internal storage
     * @param array|stdObject ...$arrays
     * @return static
     * @throws InvalidArgumentException
     */
    public function concat(...$arrays) {
        foreach ($arrays as $index => $array) {
            if ($array instanceof self) $array = $array->toArray();
            if (assert(is_array($array), sprintf("Expected parameter %d to be an array or instance of %s", $index + 1, self::class))) {
                $this->storage = array_replace_recursive($this->storage, $array);
            }
        }
        return $this;
    }

    /**
     *  Applies the callback to the elements of the storage
     * @param callable $callback a callback
     * @return static
     */
    public function map(callable $callback) {
        $result = static::create();
        foreach ($this as $key => $value) $result[$key] = $callback($value, $key);
        return $result;
    }

    /**
     * Tests if all the elements from the storage pass the test implemented by the callable
     * @param callable $callback
     * @return boolean
     */
    public function every(callable $callback) {
        foreach ($this as $key => $value) {
            if ($callback($value, $key) === false) return false;
        }
        return true;
    }

    /**
     * Tests if at least one of the elements from the storage pass the test implemented by the callable
     * @param callable $callback
     * @return boolean
     */
    public function some(callable $callback) {
        foreach ($this as $key => $value) {
            if ($callback($value, $key) !== false) return true;
        }
        return false;
    }

    /**
     * Returns a new stdObject with all the elements that passes the test
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback) {
        $result = static::create();
        foreach ($this as $key => $value) {
            if ($callback($value, $key) === true) $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Runs the given callable for each of the elements
     * @param callable $callback
     * @return $this
     */
    public function forEach(callable $callback) {
        foreach ($this as $key => $value) $callback($value, $key);
        return $this;
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
    protected function findKey(string $prop): string {
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
        $return = null;
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
        } elseif (static::$debug === true) throw new RuntimeException('Undefined index: ' . $offset, E_USER_NOTICE);
        return $return;
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
        return $this->offsetGet($this->findKey($name));
    }

    /** {@inheritdoc} */
    public function __isset($name) {
        return $this->offsetExists($this->findKey($name));
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $this->offsetSet($this->findKey($name), $value);
    }

    /** {@inheritdoc} */
    public function __unset($name) {
        $this->offsetUnset($this->findKey($name));
    }

}
