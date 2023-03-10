<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    LogicException;
use NGSOFT\{
    Tools, Type\Sort
};

trait CommonMethods
{

    /**
     * Returns an iterable indexed by id
     */
    abstract public function entries(Sort $sort = Sort::ASC): iterable;

    /**
     * Sorts array using Sort enum
     */
    protected function sortArray(array $array, Sort $sort): array
    {

        if ($sort->is(Sort::DESC))
        {
            return array_reverse($array);
        }

        return $array;
    }

    /**
     * Helper to be used with __clone() method
     */
    protected function cloneArray(array $array): array
    {
        return Tools::cloneArray($array);
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

    /**
     * Checks if empty
     */
    public function isEmpty(): bool
    {
        if ($this instanceof Countable === false)
        {
            throw new LogicException(sprintf('%s not an instance of %s', static::class, Countable::class));
        }

        return $this->count() === 0;
    }

}
