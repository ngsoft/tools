<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    Generator,
    InvalidArgumentException,
    IteratorAggregate,
    JsonSerializable,
    NGSOFT\Traits\StringableObject,
    Stringable,
    Traversable;

/**
 * Simulates Many-To-Many relations found in database
 *
 * @link https://en.wikipedia.org/wiki/Many-to-many_(data_model)
 */
final class SharedList implements Countable, IteratorAggregate, JsonSerializable, Stringable
{

    use StringableObject;

    private array $values = [];
    private array $pairs = [];
    private int $offset = -1;

    /**
     * Create a new SharedList
     *
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    public function clear(): void
    {
        $this->values = $this->pairs = [];
        $this->offset = -1;
    }

    /**
     * Checks if value exists in the set
     *
     * @param int|float|string|object $value
     * @return bool
     */
    public function hasValue(int|float|string|object $value): bool
    {
        return $this->indexOf($value) > -1;
    }

    private function indexOf(int|float|string|object $value): int
    {
        $index = array_search($value, $this->values, true);
        return $index !== false ? $index : -1;
    }

    private function indexOfPair(int|float|string|object $value, int|float|string|object $sharedValue): int
    {
        $offset = $this->indexOf($value);
        $sharedOffset = $this->indexOf($sharedValue);
        $index = array_search([$offset, $sharedOffset], $this->pairs);
        if ($index === false) $index = array_search([$sharedOffset, $offset], $this->pairs);
        return $index !== false ? $index : -1;
    }

    private function valueHasPair(int|float|string|object $value): bool
    {

        if (($index = $this->indexOf($value)) > -1) {
            foreach ($this->pairs as list($offset, $sharedOffset)) {
                if ($index === $offset || $index === $sharedOffset) return true;
            }
        }
        return false;
    }

    private function addValue(int|float|string|object $value): int
    {

        $offset = $this->indexOf($value);
        if ($offset < 0) {
            $offset = ++$this->offset;
            $this->values[$offset] = $value;
        }
        return $offset;
    }

    /**
     * Checks if relationship exists between 2 values
     *
     * @param int|float|string|object $value
     * @param int|float|string|object $sharedValue
     * @return bool
     */
    public function has(int|float|string|object $value, int|float|string|object $sharedValue): bool
    {
        return $this->indexOfPair($value, $sharedValue) > -1;
    }

    /**
     * Add a relationship between 2 values
     *
     * @param int|float|string|object $value
     * @param int|float|string|object $sharedValue
     * @return static
     * @throws InvalidArgumentException
     */
    public function add(int|float|string|object $value, int|float|string|object $sharedValue): static
    {
        if ($value === $sharedValue) {
            throw new InvalidArgumentException('Cannot add many-to-many relationship between 2 identical values.');
        }
        if (!$this->has($value, $sharedValue)) {
            $offset = $this->addValue($value);
            $sharedOffset = $this->addValue($sharedValue);
            $this->pairs[] = [$offset, $sharedOffset];
        }
        return $this;
    }

    /**
     * Removes a value and all its relationships
     *
     * @param int|float|string|object $value
     * @return static
     */
    public function deleteValue(int|float|string|object $value): static
    {

        $index = $this->indexOf($value);

        foreach (array_keys($this->pairs) as $pairIndex) {
            list($offset, $sharedOffset) = $this->pairs[$pairIndex];
            if ($offset === $index || $sharedOffset === $index) {
                unset($this->pairs[$pairIndex]);
            }
        }

        unset($this->values[$index]);
        return $this;
    }

    /**
     * Removes a relationship between 2 values
     *
     * @param int|float|string|object $value
     * @param int|float|string|object $sharedValue
     * @return static
     */
    public function delete(int|float|string|object $value, int|float|string|object $sharedValue): static
    {

        unset($this->pairs[$this->indexOfPair($value, $sharedValue)]);

        if (!$this->valueHasPair($value)) {
            $this->deleteValue($value);
        }
        if (!$this->valueHasPair($sharedValue)) {
            $this->deleteValue($sharedValue);
        }

        return $this;
    }

    /**
     * Get value shared list
     *
     * @param int|float|string|object $value
     * @return Set
     */
    public function get(int|float|string|object $value): Set
    {

        $result = new Set();
        if (($index = $this->indexOf($value)) > -1) {

            $index = $this->indexOf($value);
            foreach ($this->pairs as list($offset, $sharedOffset)) {
                if ($offset === $index) {
                    $result->add($this->values[$sharedOffset]);
                } elseif ($sharedOffset === $index) {
                    $result->add($this->values[$offset]);
                }
            }
        }
        return $result;
    }

    /**
     * Iterates all values shared lists
     *
     * @return Generator
     */
    public function entries(): Generator
    {
        foreach ($this->values as $offset => $value) {
            yield $value => $this->get($value);
        }
    }

    public function count(): int
    {
        return count($this->pairs);
    }

    public function getIterator(): Traversable
    {
        yield from $this->entries();
    }

    public function jsonSerialize(): mixed
    {
        return [];
    }

    public function __serialize(): array
    {
        return [$this->values, $this->pairs, $this->offset];
    }

    public function __unserialize(array $data)
    {
        list($this->values, $this->pairs, $this->offset) = $data;
    }

    public function __debugInfo(): array
    {
        $info = [];
        foreach ($this as $value => $set) { $info[] = [$value, $set]; }
        return $info;
    }

}
