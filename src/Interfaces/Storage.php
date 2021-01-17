<?php

declare(strict_types=1);

namespace NGSOFT\Interfaces;

interface Storage {

    /**
     * When passed a key name, checks if it exists in the storage.
     * @param string $key
     * @return bool
     */
    public function hasItem(string $key): bool;

    /**
     * When passed a key name, will return that key's value.
     * @param string $key
     * @return mixed
     */
    public function getItem(string $key);

    /**
     * When passed a key name and value, will add that key to the storage, or update that key's
     * @param string $key
     * @param mixed $value
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
     * Clears the Storage.
     * @return void
     */
    public function clear(): void;
}
