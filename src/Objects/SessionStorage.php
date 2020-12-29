<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use ArrayAccess,
    Countable,
    Iterator;
use NGSOFT\Tools\{
    Interfaces\Storage, Traits\ArrayAccessCountable, Traits\ArrayAccessIterator
};
use RuntimeException;

class SessionStorage implements ArrayAccess, Countable, Iterator, Storage {

    use ArrayAccessCountable,
        ArrayAccessIterator;

    /** @var array */
    protected $storage = [];

    public function __construct() {
        if (empty(session_id())) session_start();
        $this->storage = &$_SESSION;
    }

    ////////////////////////////   Overrides   ////////////////////////////

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        if (
                $this->storage === $_SESSION
                and (is_int($offset) or is_null($offset))
        ) {
            throw new RuntimeException("Trying to get a numeric key on session");
        }

        $value = null;
        if ($offset === null) {
            $this->storage[] = [];
            $offset = array_key_last($this->storage);
        }
        if (!$this->offsetExists($offset)) return $value;
        if (is_array($this->storage[$offset])) {
            //link sub arrays
            $value = &$this->storage[$offset];
            $instance = clone $this;
            $instance->storage = &$value;
            return $instance;
        } else $value = $this->storage[$offset];
        return $value;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if (
                $this->storage === $_SESSION
                and (is_int($offset) or is_null($offset))
        ) {
            throw new RuntimeException("Trying to set a numeric key on session");
        }
        if ($value instanceof self) {
            $value = $value->storage;
        }
        if ($offset === null) $this->storage[] = $value;
        else $this->storage[$offset] = $value;
    }

    ////////////////////////////   Storage   ////////////////////////////

    /** {@inheritdoc} */
    public function clear(): void {

        foreach (array_keys($this->storage) as $key) {
            unset($this->storage[$key]);
        }
    }

    /** {@inheritdoc} */
    public function getItem(string $key) {
        return $this->offsetGet($key);
    }

    /** {@inheritdoc} */
    public function removeItem(string $key): void {
        $this->offsetUnset($key);
    }

    /** {@inheritdoc} */
    public function setItem(string $key, $value): void {
        $this->offsetSet($key, $value);
    }

    ////////////////////////////   Getters/Setters   ////////////////////////////

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
    public function &__get($name) {
        $value = $this->offsetGet($name);
        return $value;
    }

}
