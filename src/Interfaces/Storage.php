<?php

namespace NGSOFT\Tools\Interfaces;

interface Storage extends \Traversable, \Countable, \ArrayAccess {

    /**
     * Returns an integer representing the number of data items stored
     * @return int
     */
    public function length(): int;

    /**
     * When passed a key name, will return that key's value.
     * @param string $key
     * @return mixed
     */
    public function getItem(string $key);

    /**
     * When passed a key name and value, will add that key to the storage, or update that key's
     * @param string $key
     * @param type $value
     * @return void
     */
    public function setItem(string $key, $value): void;

    /**
     * When passed a key name, will remove that key from the storage.
     * @param string $key
     * @return void
     */
    public function removeItem(string $key): void;

    /**
     * When passed a key name, will remove that key from the storage.
     * @return void
     */
    public function clear(): void;
}
