<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    Generator,
    IteratorAggregate,
    JsonSerializable;
use NGSOFT\{
    Traits\StringableObject, Types\Sort
};
use Stringable,
    Traversable;

/**
 * The Set object lets you store unique values of any type, whether primitive values or object references.
 */
final class Set implements Countable, JsonSerializable, Stringable, IteratorAggregate
{

    use StringableObject,
        CommonMethods;

    private array $storage = [];

    /**
     * Create a new Set
     *
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * Get Index of value inside the set
     *
     * @param mixed $value
     * @return int
     */
    private function indexOf(mixed $value): int
    {
        $index = array_search($value, $this->storage, true);
        return $index !== false ? $index : -1;
    }

    private function getIndexes(Sort $sort = Sort::ASC): Generator
    {
        yield from $this->sortArray(array_keys($this->storage), $sort);
    }

    /**
     * The add() method appends a new element with a specified value to the end of a Set object.
     */
    public function add(mixed $value): static
    {
        if ( ! $this->has($value)) { $this->storage[] = $value; }
        return $this;
    }

    /**
     * The clear() method removes all elements from a Set object.
     */
    public function clear(): void
    {
        $this->storage = [];
    }

    /**
     * The delete() method removes a specified value from a Set object, if it is in the set.
     */
    public function delete(mixed $value): bool
    {
        $offset = $this->indexOf($value);
        if ($offset > -1) {
            unset($this->storage[$offset]);
            return true;
        }
        return false;
    }

    /**
     * The entries() method returns a new Iterator object that contains an array of [value, value] for each element in the Set object, in insertion order.
     */
    public function entries(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->getIndexes($sort) as $offset) {
            yield $this->storage[$offset] => $this->storage[$offset];
        }
    }

    /**
     * The has() method returns a boolean indicating whether an element with the specified value exists in a Set object or not.
     */
    public function has(mixed $value): bool
    {
        return $this->indexOf($value) !== -1;
    }

    /**
     * Checks if set is empty
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * The values() method returns a new Iterator object that contains the values for each element in the Set object in insertion order.
     */
    public function values(Sort $sort = Sort::ASC): iterable
    {
        yield from $this->sortArray(array_values($this->storage), $sort);
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        yield from $this->values();
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return $this->storage;
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return $this->storage;
    }

    public function __serialize(): array
    {
        return $this->storage;
    }

    public function __unserialize(array $data): void
    {
        $this->storage = $data;
    }

    /** {@inheritdoc} */
    public function __clone(): void
    {
        $this->storage = $this->cloneArray($this->storage);
    }

}
