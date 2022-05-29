<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ValueError;

/**
 * Simulates one to many relationships found in databases
 */
final class OwnedList implements \Countable, \Stringable, \IteratorAggregate, \JsonSerializable
{

    private Set $ownedList;

    /**
     * Creates a new OwnedList for the given value
     *
     * @param int|float|string|object $value
     * @return static
     */
    public static function create(int|float|string|object $value)
    {
        return new static($value);
    }

    public function __construct(
            public readonly int|float|string|object $value
    )
    {
        $this->ownedList = new Set();
    }

    /**
     * Adds a relationship between current value and the given value
     *
     * @param int|float|string|object $value
     * @return static
     * @throws ValueError
     */
    public function add(int|float|string|object $value): static
    {

        if ($this->value === $value) {
            throw new ValueError(sprintf('Value cannot own itself.'));
        }
        $this->ownedList->add($value);
        return $this;
    }

    /**
     * Removes a relationship between current value and the given value
     *
     * @param int|float|string|object $value
     * @return bool
     */
    public function delete(int|float|string|object $value): bool
    {
        return $this->ownedList->delete($value);
    }

    /**
     * Checks if a relationship exists between current value and the given value
     *
     * @param int|float|string|object $value
     * @return bool
     */
    public function has(int|float|string|object $value): bool
    {
        return $this->ownedList->has($value);
    }

    /**
     * Removes all relationships
     *
     * @return void
     */
    public function clear(): void
    {
        $this->ownedList->clear();
    }

    /**
     * Iterates entries
     *
     * @return Generator
     */
    public function entries(): Generator
    {
        foreach ($this->ownedList as $owned) { yield $this->value => $owned; }
    }

    /**
     * Iterates owned values
     *
     * @return Generator
     */
    public function values(): Generator
    {
        foreach ($this->entries() as $value) { yield $value; }
    }

    public function count(): int
    {
        return count($this->ownedList);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->entries();
    }

    public function jsonSerialize(): mixed
    {
        return [];
    }

    /** {@inheritdoc} */
    public function __toString()
    {
        return sprintf('[object %s]', static::class);
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->value, $this->ownedList];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {
        list($this->value, $this->ownedList) = $data;
    }

}
