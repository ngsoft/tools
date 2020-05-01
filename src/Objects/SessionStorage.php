<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use Iterator;
use NGSOFT\Tools\{
    Exceptions\RuntimeException, Interfaces\Storage, Traits\ArrayAccessIterator, Traits\ArrayAccessCountable
};

class SessionStorage implements Iterator, Storage {

    use ArrayAccessCountable,
        ArrayAccessIterator;

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
        return $this->storage[$key] ?? null;
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
        return $this->offsetGet($name);
    }

}
