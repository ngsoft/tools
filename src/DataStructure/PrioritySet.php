<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    Generator,
    IteratorAggregate,
    JsonSerializable,
    NGSOFT\Traits\StringableObject,
    RuntimeException,
    Stringable,
    Traversable;

/**
 * A Priority Set is a set that sorts entries by priority
 */
class PrioritySet implements Countable, JsonSerializable, Stringable, IteratorAggregate
{

    use StringableObject;

    private array $priorities = [];
    private array $storage = [];

    /** @var array[] */
    private array $sorted = [];

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

    /** @return array[] */
    private function getSorted(): array
    {
        if (empty($this->storage)) {
            return [];
        } elseif (empty($this->sorted)) {
            $sorted = &$this->sorted;

            foreach ($this->priorities as $offset => $priority) {

                $sorted[$priority] ??= [];
                $sorted[$priority] [] = $offset;
            }

            krsort($sorted);
        }

        return $this->sorted;
    }

    private function getIndexes(Sort $sort = Sort::DESC): Generator
    {

        $sorted = $this->getSorted();
        if ($sort->is(Sort::ASC)) {
            $sorted = array_reverse($sorted);
        }

        foreach ($sorted as $offsets) {
            yield from $offsets;
        }
    }

    /**
     * The add() method adds a new element with a specified value with a given priority.
     *
     * @param mixed $value
     * @param int|Priority $priority > 0 the highest the number, the highest the priority
     * @return static
     */
    public function add(mixed $value, int|Priority $priority = Priority::MEDIUM): static
    {

        $priority = is_int($priority) ? $priority : $priority->getValue();

        if ( ! $this->has($value)) {
            $this->storage[] = $value;
            $this->priorities[$this->indexOf($value)] = max(1, $priority);
            //reset sorted
            $this->sorted = [];
        }
        return $this;
    }

    public function getPriority(mixed $value): int
    {
        $offset = $this->indexOf($value);

        if ($offset < 0) {
            return $offset;
        }

        return $this->priorities[$offset];
    }

    /**
     * The clear() method removes all elements from a Set object.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->storage = $this->priorities = $this->sorted = [];
    }

    /**
     * The delete() method removes a specified value from a Set object, if it is in the set.
     *
     * @param mixed $value
     * @return bool
     */
    public function delete(mixed $value): bool
    {
        $offset = $this->indexOf($value);
        if ($offset > -1) {
            unset($this->storage[$offset], $this->priorities[$offset]);
            $this->sorted = [];
            return true;
        }
        return false;
    }

    /**
     * The entries() method returns a new Iterator object that contains an array of [value, value] for each element in the Set object, in insertion order.
     */
    public function entries(Sort $sort = Sort::DESC): Generator
    {
        foreach ($this->getIndexes($sort) as $offset) { yield $this->storage[$offset] => $this->storage[$offset]; }
    }

    /**
     * The forEach() method executes a provided function once for each value in the Set object, in insertion order.
     *
     * @param callable $callable ($value,$value, Set)
     * @return void
     */
    public function forEach(callable $callable): void
    {
        foreach ($this->entries() as $value) { $callable($value, $value, $this); }
    }

    /**
     * The has() method returns a boolean indicating whether an element with the specified value exists in a Set object or not.
     *
     * @param mixed $value
     * @return bool
     */
    public function has(mixed $value): bool
    {
        return $this->indexOf($value) !== -1;
    }

    /**
     * Checks if set is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * The values() method returns a new Iterator object that contains the values for each element in the Set object in insertion order.
     */
    public function values(Sort $sort = Sort::DESC): Generator
    {
        foreach ($this->entries($sort) as $value) { yield $value; }
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        yield from $this->entries();
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return iterator_to_array($this->values());
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    public function __serialize(): array
    {
        return [$this->storage, $this->priorities];
    }

    public function __unserialize(array $data): void
    {
        list($this->storage, $this->priorities) = $data;
    }

    /** {@inheritdoc} */
    public function __clone(): void
    {

        $this->sorted = [];
        $indexes = array_keys($this->storage);
        foreach ($indexes as $index) {
            if (is_object($this->storage[$index])) {
                $this->storage[$index] = clone $this->storage[$index];
            }
        }
    }

}
