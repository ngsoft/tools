<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Traits\StringableObject;

/**
 * Simulates Many-To-Many relations found in database.
 *
 * @see https://en.wikipedia.org/wiki/Many-to-many_(data_model)
 */
final class SharedList implements \Countable, \IteratorAggregate, \JsonSerializable, \Stringable
{
    use StringableObject;

    use CommonMethods;

    private array $values = [];
    private array $pairs  = [];
    private int $offset   = -1;

    public function __serialize(): array
    {
        return [$this->values, $this->pairs, $this->offset];
    }

    public function __unserialize(array $data): void
    {
        list($this->values, $this->pairs, $this->offset) = $data;
    }

    public function __debugInfo(): array
    {
        $info = [];

        foreach ($this as $value => $set)
        {
            $info[] = [$value, $set];
        }
        return $info;
    }

    /**
     * Create a new SharedList.
     */
    public static function create(): static
    {
        return new self();
    }

    public function clear(): void
    {
        $this->values = $this->pairs = [];
        $this->offset = -1;
    }

    /**
     * Checks if value exists in the set.
     */
    public function hasValue(float|int|object|string $value): bool
    {
        return $this->indexOf($value) > -1;
    }

    /**
     * Checks if relationship exists between 2 values.
     */
    public function has(float|int|object|string $value, float|int|object|string $sharedValue): bool
    {
        return $this->indexOfPair($value, $sharedValue) > -1;
    }

    /**
     * Add a relationship between 2 values.
     *
     * @throws \InvalidArgumentException
     */
    public function add(float|int|object|string $value, float|int|object|string $sharedValue): static
    {
        if ($value === $sharedValue)
        {
            throw new \InvalidArgumentException('Cannot add many-to-many relationship between 2 identical values.');
        }

        if ( ! $this->has($value, $sharedValue))
        {
            $offset        = $this->addValue($value);
            $sharedOffset  = $this->addValue($sharedValue);
            $this->pairs[] = [$offset, $sharedOffset];
        }
        return $this;
    }

    /**
     * Removes a value and all its relationships.
     */
    public function deleteValue(float|int|object|string $value): static
    {
        $index = $this->indexOf($value);

        foreach (array_keys($this->pairs) as $pairIndex)
        {
            list($offset, $sharedOffset) = $this->pairs[$pairIndex];

            if ($offset === $index || $sharedOffset === $index)
            {
                unset($this->pairs[$pairIndex]);
            }
        }

        unset($this->values[$index]);
        return $this;
    }

    /**
     * Removes a relationship between 2 values.
     */
    public function delete(float|int|object|string $value, float|int|object|string $sharedValue): static
    {
        unset($this->pairs[$this->indexOfPair($value, $sharedValue)]);

        if ( ! $this->valueHasPair($value))
        {
            $this->deleteValue($value);
        }

        if ( ! $this->valueHasPair($sharedValue))
        {
            $this->deleteValue($sharedValue);
        }

        return $this;
    }

    /**
     * Get value shared list.
     */
    public function get(float|int|object|string $value): Set
    {
        $result = new Set();

        if (($index = $this->indexOf($value)) > -1)
        {
            foreach ($this->pairs as list($offset, $sharedOffset))
            {
                if ($offset === $index)
                {
                    $result->add($this->values[$sharedOffset]);
                } elseif ($sharedOffset === $index)
                {
                    $result->add($this->values[$offset]);
                }
            }
        }
        return $result;
    }

    /**
     * Iterates all values shared lists.
     */
    public function entries(Sort $sort = Sort::ASC): iterable
    {
        $values = $this->values;

        if (Sort::DESC === $sort)
        {
            $values = array_reverse($values);
        }

        foreach ($values as $value)
        {
            yield $value => $this->get($value);
        }
    }

    public function count(): int
    {
        return count($this->pairs);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->entries();
    }

    public function jsonSerialize(): mixed
    {
        return [];
    }

    private function indexOf(float|int|object|string $value): int
    {
        $index = array_search($value, $this->values, true);
        return false !== $index ? $index : -1;
    }

    private function indexOfPair(float|int|object|string $value, float|int|object|string $sharedValue): int
    {
        $offset       = $this->indexOf($value);
        $sharedOffset = $this->indexOf($sharedValue);
        $index        = array_search([$offset, $sharedOffset], $this->pairs);

        if (false === $index)
        {
            $index = array_search([$sharedOffset, $offset], $this->pairs);
        }
        return false !== $index ? $index : -1;
    }

    private function valueHasPair(float|int|object|string $value): bool
    {
        if (($index = $this->indexOf($value)) > -1)
        {
            foreach ($this->pairs as list($offset, $sharedOffset))
            {
                if ($index === $offset || $index === $sharedOffset)
                {
                    return true;
                }
            }
        }
        return false;
    }

    private function addValue(float|int|object|string $value): int
    {
        $offset = $this->indexOf($value);

        if ($offset < 0)
        {
            $offset                = ++$this->offset;
            $this->values[$offset] = $value;
        }
        return $offset;
    }
}
