<?php

namespace NGSOFT\Tools\Objects;

use IteratorAggregate;
use NGSOFT\Tools\Interfaces\Storage;
use NGSOFT\Tools\Traits\ArrayAccessTrait;

class SessionStorage implements Storage, IteratorAggregate {

    use ArrayAccessTrait;

    /** @var array */
    private $storage;

    public function __construct() {
        if (session_id() === "") session_start();
        $this->storage = &$_SESSION;
    }

    public function clear(): void {
        foreach (array_keys($this->storage) as $key) {
            unset($this->storage[$key]);
        }
    }

    public function getItem(string $key) {
        return $this->offsetGet($key);
    }

    public function length(): int {
        return count($this->storage);
    }

    public function removeItem(string $key): void {
        $this->offsetUnset($key);
    }

    public function setItem(string $key, $value): void {
        $this->offsetSet($offset, $value);
    }

    public function __set($name, $value) {
        $this->setItem($name, $value);
    }

    public function __unset($name) {
        $this->removeItem($name);
    }

    public function __get($name) {
        return $this->getItem($name);
    }

}
