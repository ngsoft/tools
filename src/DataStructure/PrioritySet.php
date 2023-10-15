<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Traits\ObjectLock;
use NGSOFT\Traits\StringableObject;

/**
 * A Priority Set is a set that sorts entries by priority.
 */
class PrioritySet implements \Countable, \JsonSerializable, \Stringable, \IteratorAggregate
{
    use StringableObject;

    use CommonMethods;

    use ObjectLock;

    private array $priorities = [];
    private array $storage    = [];

    /** @var array[] */
    private array $sorted     = [];

    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    public function __serialize(): array
    {
        return [$this->storage, $this->priorities, $this->locked];
    }

    public function __unserialize(array $data): void
    {
        list($this->storage, $this->priorities, $this->locked) = $data;
    }

    public function __clone(): void
    {
        $this->sorted  = [];
        $this->storage = $this->cloneArray($this->storage);
    }

    /**
     * Create a new Set.
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * The add() method adds a new element with a specified value with a given priority.
     *
     * @param int|Priority $priority > 0 the highest the number, the highest the priority
     */
    public function add(mixed $value, int|Priority $priority = Priority::MEDIUM): static
    {
        if ($this->isLocked())
        {
            return $this;
        }

        $priority = is_int($priority) ? $priority : $priority->getValue();

        if ( ! $this->has($value))
        {
            $this->storage[]                          = $value;
            $this->priorities[$this->indexOf($value)] = max(1, $priority);
            // reset sorted
            $this->sorted                             = [];
        }
        return $this;
    }

    public function getPriority(mixed $value): int
    {
        $offset = $this->indexOf($value);

        if ($offset < 0)
        {
            return $offset;
        }

        return $this->priorities[$offset];
    }

    /**
     * The clear() method removes all elements from a Set object.
     */
    public function clear(): void
    {
        if ($this->isLocked())
        {
            return;
        }

        $this->storage = $this->priorities = $this->sorted = [];
    }

    /**
     * The delete() method removes a specified value from a Set object, if it is in the set.
     */
    public function delete(mixed $value): bool
    {
        if ( ! $this->isLocked())
        {
            $offset = $this->indexOf($value);

            if ($offset > -1)
            {
                unset($this->storage[$offset], $this->priorities[$offset]);
                $this->sorted = [];
                return true;
            }
        }
        return false;
    }

    /**
     * The entries() method returns a new Iterator object that contains an array of [value, value] for each element in the Set object, in insertion order.
     */
    public function entries(Sort $sort = Sort::DESC): iterable
    {
        foreach ($this->getIndexes($sort) as $offset)
        {
            yield $this->storage[$offset] => $this->storage[$offset];
        }
    }

    /**
     * The forEach() method executes a provided function once for each value in the Set object, in insertion order.
     *
     * @param callable $callable ($value,$value, Set)
     */
    public function forEach(callable $callable): void
    {
        foreach ($this->entries() as $value)
        {
            $callable($value, $value, $this);
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
    public function values(Sort $sort = Sort::DESC): \Generator
    {
        foreach ($this->entries($sort) as $value)
        {
            yield $value;
        }
    }

    public function count(): int
    {
        return count($this->storage);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->entries();
    }

    public function jsonSerialize(): mixed
    {
        return iterator_to_array($this->values());
    }

    /**
     * Get Index of value inside the set.
     */
    private function indexOf(mixed $value): int
    {
        $index = array_search($value, $this->storage, true);
        return false !== $index ? $index : -1;
    }

    /** @return array[] */
    private function getSorted(): array
    {
        if (empty($this->storage))
        {
            return [];
        }

        if (empty($this->sorted))
        {
            $sorted = &$this->sorted;

            foreach ($this->priorities as $offset => $priority)
            {
                $sorted[$priority] ??= [];
                $sorted[$priority][] = $offset;
            }

            krsort($sorted);
        }

        return $this->sorted;
    }

    private function getIndexes(Sort $sort = Sort::DESC): iterable
    {
        $sorted = $this->getSorted();

        if (Sort::ASC === $sort)
        {
            $sorted = array_reverse($sorted);
        }

        foreach ($sorted as $offsets)
        {
            yield from $offsets;
        }
    }
}
