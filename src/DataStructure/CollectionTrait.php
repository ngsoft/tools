<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Tools;

trait CollectionTrait
{

    protected array $storage = [];

    /**
     * Returns a new iterable indexed by id
     */
    abstract public function entries(Sort $sort = Sort::ASC): iterable;

    /**
     * Create a new instance
     */
    abstract protected function createNew(): static;

    /**
     * Exports to array
     */
    public function toArray(): array
    {
        return $this->storage;
    }

    /**
     * Clears the Storage
     *
     * @return void
     */
    public function clear(): void
    {
        $array = [];
        $this->storage = &$array;
    }

    /**
     * Applies the callback to the elements of the storage and returns a copy
     */
    public function map(callable $callback): static
    {
        $result = $this->createNew();

        foreach ($this->entries() as $offset => $value) {

            $newValue = $callback($value, $offset, $this);

            if ($newValue === null) {
                $newValue = $value;
            }

            $result->offsetSet(is_string($offset) ? $offset : null, $newValue);
        }

        return $result;
    }

    /**
     * Returns a copy with all the elements that passes the test
     */
    public function filter(callable $callback): static
    {
        $result = $this->createNew();

        foreach ($this->entries() as $offset => $value) {

            if ( ! $callback($value, $offset, $this)) {
                continue;
            }
            if ( ! is_string($offset)) {
                $offset = null;
            }

            $result->offsetSet($offset, $value);
        }

        return $result;
    }

    /**
     * Checks if value in the storage
     */
    public function has(mixed $value): bool
    {

        if ($value instanceof self) {
            $value = $value->storage;
        }
        return in_array($value, $this->storage, true);
    }

    /**
     * Tests if at least one of the elements from the storage pass the test implemented by the callable
     */
    public function some(callable $callback): bool
    {
        return Tools::some($callback, $this->entries());
    }

    /**
     * Tests if all the elements from the storage pass the test implemented by the callable
     */
    public function every(callable $callback): bool
    {
        return Tools::every($callback, $this->entries());
    }

    /**
     * Runs the given callable for each of the elements
     */
    public function each(callable $callback): void
    {
        Tools::each($callback, $this->entries());
    }

    protected function clone(array $array): array
    {

        foreach ($array as $offset => $value) {

            if (is_object($value)) {
                $array[$offset] = clone $value;
            }


            if (is_array($value)) {
                $array[$offset] = $this->clone($value);
            }
        }

        return $array;
    }

    public function __clone(): void
    {
        $this->storage = $this->clone($this->storage);
    }

}
