<?php

namespace NGSOFT\Tools\Objects;

use ArrayIterator;
use IteratorAggregate;
use NGSOFT\Tools\Interfaces\Storage;
use NGSOFT\Tools\Traits\ArrayAccessTrait;

class SessionStorage implements IteratorAggregate, Storage {

    use ArrayAccessTrait;

    public function __construct() {
        if (empty(session_id())) session_start();
        print_r(session_id());
        $this->storage = &$_SESSION;
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

}
