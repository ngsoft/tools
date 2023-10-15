<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Traits\ObjectLock;
use NGSOFT\Traits\ReversibleIteratorTrait;
use NGSOFT\Traits\StringableObject;

/**
 * Simulates one to many relationships found in databases.
 */
final class OwnedList implements \Stringable, ReversibleIterator, \JsonSerializable, \ArrayAccess
{
    use StringableObject;

    use ObjectLock;

    use ReversibleIteratorTrait;

    private Set $ownedList;

    public function __construct(
        public readonly int|float|string|object $value
    ) {
        $this->ownedList = new Set();
    }

    public function __debugInfo(): array
    {
        return [
            'value'     => $this->value,
            'ownedList' => $this->ownedList,
            'locked'    => $this->locked,
        ];
    }

    public function __serialize(): array
    {
        return [$this->value, $this->ownedList, $this->locked];
    }

    public function __unserialize(array $data): void
    {
        list($this->value, $this->ownedList, $this->locked) = $data;
    }

    public function __clone(): void
    {
        throw new \RuntimeException(sprintf('%s cannot be cloned.', self::class));
    }

    /**
     * Creates a new OwnedList for the given value.
     *
     * @return static
     */
    public static function create(int|float|string|object $value)
    {
        return new self($value);
    }

    /**
     * Adds a relationship between current value and the given value.
     *
     * @throws \ValueError
     */
    public function add(int|float|string|object $value): static
    {
        if ($this->value === $value)
        {
            throw new \ValueError('Value cannot own itself.');
        }

        if ( ! $this->isLocked())
        {
            $this->ownedList->add($value);
        }

        return $this;
    }

    /**
     * Removes a relationship between current value and the given value.
     */
    public function delete(int|float|string|object $value): bool
    {
        if ($this->isLocked())
        {
            return false;
        }
        return $this->ownedList->delete($value);
    }

    /**
     * Checks if a relationship exists between current value and the given value.
     */
    public function has(int|float|string|object $value): bool
    {
        return $this->ownedList->has($value);
    }

    /**
     * Removes all relationships.
     */
    public function clear(): void
    {
        if ($this->isLocked())
        {
            return;
        }
        $this->ownedList->clear();
    }

    /**
     * Iterates entries.
     *
     * @return \Generator
     */
    public function entries(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->ownedList->entries($sort) as $owned)
        {
            yield $this->value => $owned;
        }
    }

    /**
     * Iterates owned values.
     */
    public function values(): \Generator
    {
        foreach ($this->entries() as $value)
        {
            yield $value;
        }
    }

    public function count(): int
    {
        return count($this->ownedList);
    }

    public function offsetExists(mixed $offset): bool
    {
        return false;
    }

    public function offsetGet(mixed $offset): mixed
    {
        throw new \OutOfBoundsException(sprintf('%s does not have keys.', static::class));
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null !== $offset)
        {
            $this->offsetGet($offset);
        }
        $this->add($value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->offsetGet($offset);
    }

    public function jsonSerialize(): mixed
    {
        return [];
    }
}
