<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Traits\ReversibleIteratorTrait;
use NGSOFT\Traits\StringableObject;

/**
 * The Set object lets you store unique values of any type, whether primitive values or object references.
 */
final class Set implements ReversibleIterator, \JsonSerializable, \Stringable
{
    use StringableObject;

    use CommonMethods;

    use ReversibleIteratorTrait;

    private array $storage = [];

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

    public function __clone(): void
    {
        $this->storage = $this->cloneArray($this->storage);
    }

    /**
     * Create a new Set.
     */
    public static function create(): static
    {
        return new self();
    }

    /**
     * The add() method appends a new element with a specified value to the end of a Set object.
     */
    public function add(mixed $value): static
    {
        if ( ! $this->has($value))
        {
            $this->storage[] = $value;
        }
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

        if ($offset > -1)
        {
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
        foreach ($this->getIndexes($sort) as $offset)
        {
            yield $this->storage[$offset] => $this->storage[$offset];
        }
    }

    /**
     * The has() method returns a boolean indicating whether an element with the specified value exists in a Set object or not.
     */
    public function has(mixed $value): bool
    {
        return -1 !== $this->indexOf($value);
    }

    /**
     * Checks if set is empty.
     */
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * The values() method returns a new Iterator object that contains the values for each element in the Set object in insertion order.
     */
    public function values(Sort $sort = Sort::ASC): iterable
    {
        yield from $this->sortArray(array_values($this->storage), $sort);
    }

    public function count(): int
    {
        return count($this->storage);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->values();
    }

    public function jsonSerialize(): mixed
    {
        return $this->storage;
    }

    /**
     * Get Index of value inside the set.
     */
    private function indexOf(mixed $value): int
    {
        $index = array_search($value, $this->storage, true);
        return false !== $index ? $index : -1;
    }

    private function getIndexes(Sort $sort = Sort::ASC): \Generator
    {
        yield from $this->sortArray(array_keys($this->storage), $sort);
    }
}
