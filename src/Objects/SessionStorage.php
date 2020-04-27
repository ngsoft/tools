<?php

namespace NGSOFT\Tools\Objects;

use Iterator;
use NGSOFT\Tools\{
    Exceptions\RuntimeException, Interfaces\Storage, Traits\ArrayAccessTrait
};

class SessionStorage implements Iterator, Storage {

    use ArrayAccessTrait;

    public function __construct() {
        if (empty(session_id())) session_start();
        $this->storage = &$_SESSION;
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        $return = null;
        if ($offset === null) {
            $this->offsetSet(null, []);
            $offset = array_key_last($this->storage);
        }
        if (array_key_exists($offset, $this->storage)) {
            if (is_array($this->storage[$offset])) {
                $array = &$this->storage[$offset];
                $return = clone $this;
                $return->storage = &$array;
            } else $return = $this->storage[$offset];
        }
        return $return;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if ($offset === null) {
            if ($this->storage === $_SESSION) throw new RuntimeException("Trying to set a numeric key on session");
            $this->storage[] = $value;
        } else $this->storage[$offset] = $value;
    }

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
    public function length(): int {
        return $this->count();
    }

    /** {@inheritdoc} */
    public function removeItem(string $key): void {
        $this->offsetUnset($key);
    }

    /** {@inheritdoc} */
    public function setItem(string $key, $value): void {
        $this->offsetSet($key, $value);
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $this->setItem($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset($name) {
        $this->removeItem($name);
    }

    /** {@inheritdoc} */
    public function __get($name) {
        return $this->getItem($name);
    }

    /** {@inheritdoc} */
    public function current() {
        $current = current($this->storage);
        if (is_array($current)) {
            $key = $this->key();
            $return = clone $this;
            $return->storage = &$this->storage[$key];
            return $return;
        } else return $current;
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
        return key($this->storage) !== null;
    }

}
