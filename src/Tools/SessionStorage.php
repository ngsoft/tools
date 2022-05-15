<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ArrayAccess,
    Countable,
    IteratorAggregate;
use NGSOFT\{
    Interfaces\Storage, Traits\ArrayAccessCountable
};
use RuntimeException;

class SessionStorage implements ArrayAccess, Countable, IteratorAggregate, Storage {

    use ArrayAccessCountable;

    /** @var array */
    protected $storage = [];

    public function __construct() {
        if (empty(session_id())) {
            // firefox > 84 will refuse session cookies if SameSite=None; for non ssl server
            var_dump(session_set_cookie_params([
                "samesite" => "Strict", //none, lax, strict
                "secure" => true, //false, true
                "httponly" => true, //false, true JS don't need that cookie
            ]));
            session_start();
        }
        $this->storage = &$_SESSION;
    }

    ////////////////////////////   Overrides   ////////////////////////////

    /** {@inheritdoc} */
    public function &offsetGet(mixed $offset): mixed {
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
    public function offsetSet(mixed $offset, mixed $value): void {
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
    public function getItem(string $key): mixed {
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

    /** {@inheritdoc} */
    public function hasItem(string $key): bool {
        return $this->offsetExists($key);
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
