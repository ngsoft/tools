<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Tools;

/**
 * @phan-file-suppress PhanTypeMismatchReturn
 */
trait CollectionTrait
{

    protected array $storage = [];

    /**
     * Returns a new iterable indexed by id
     */
    abstract public function entries(Sort $sort = Sort::ASC): iterable;

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
