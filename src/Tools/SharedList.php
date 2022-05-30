<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ArrayAccess,
    Countable,
    Generator,
    InvalidArgumentException,
    IteratorAggregate,
    JsonSerializable,
    Stringable,
    Traversable;

/**
 * Simulates Many-To-Many relations found in database
 *
 * @link https://en.wikipedia.org/wiki/Many-to-many_(data_model)
 */
final class SharedList implements Countable, IteratorAggregate, JsonSerializable, Stringable, ArrayAccess
{

    private Set $values;

    /** @var OwnedList[] */
    private array $ownedLists = [];

    /**
     * Create a new SharedList
     *
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    public function __construct()
    {
        $this->values = Set::create();
    }

    public function clear(): void
    {

        $this->ownedLists = [];
        $this->values->clear();
    }

    /**
     * Checks if relationship exists between 2 values
     *
     * @param int|float|string|object $value1
     * @param int|float|string|object $value2
     * @return bool
     */
    public function has(
            int|float|string|object $value1,
            int|float|string|object $value2
    ): bool
    {
        if ($list = $this->getOwnedList($value1)) {
            return $list->has($value2);
        }
        return false;
    }

    /**
     * Checks if value exists in the set
     *
     * @param int|float|string|object $value
     * @return bool
     */
    public function hasValue(int|float|string|object $value): bool
    {
        return $this->values->has($value);
    }

    /**
     * Add a relationship between 2 values
     *
     * @param int|float|string|object $value1
     * @param int|float|string|object $value2
     * @return static
     * @throws InvalidArgumentException
     */
    public function add(
            int|float|string|object $value1,
            int|float|string|object $value2
    ): static
    {
        if ($value1 === $value2) {
            throw new InvalidArgumentException('Cannot add many-to-many relationship between 2 identical values.');
        }
        $this->values
                ->add($value1)
                ->add($value2);
        $offset1 = $this->values->indexOf($value1);
        $offset2 = $this->values->indexOf($value2);
        $this->ownedLists[$offset1] = $this->ownedLists[$offset1] ?? new OwnedList($value1);
        $this->ownedLists[$offset2] = $this->ownedLists[$offset2] ?? new OwnedList($value2);
        $this->ownedLists[$offset1]->add($value2);
        $this->ownedLists[$offset2]->add($value1);
        return $this;
    }

    /**
     * Removes relationship between 2 values
     *
     * @param int|float|string|object $value1
     * @param int|float|string|object $value2
     * @return bool
     * @throws InvalidArgumentException
     */
    public function delete(
            int|float|string|object $value1,
            int|float|string|object $value2
    ): bool
    {

        if ($value1 === $value2) {
            throw new InvalidArgumentException('Cannot remove many-to-many relationship between 2 identical values.');
        }


        if (
                ($list1 = $this->getOwnedList($value1) ) &&
                ($list2 = $this->getOwnedList($value2))
        ) {

            $result = $list1->delete($value2);
            $result = $list2->delete($value1) && $result;

            if (count($list1) === 0) {
                $result = $this->deleteValue($value1) && $result;
            }
            if (count($list2) === 0) {
                $result = $this->deleteValue($value2) && $result;
            }


            return $result;
        }

        return false;
    }

    /**
     * Removes a single value and all relationships with that value
     *
     * @param int|float|string|object $value
     * @return bool
     */
    public function deleteValue(int|float|string|object $value): bool
    {

        $offset = $this->values->indexOf($value);
        if ($offset > -1) {
            if ($list = $this->getOwnedList($value)) {
                $result = true;
                foreach ($list as $otherValue) {
                    $result = $this->delete($value, $otherValue) && $result;
                }
                unset($this->ownedLists[$offset]);
                $result = $this->values->delete($value) && $result;
                return $result;
            }
        }

        return false;
    }

    private function getOwnedList(int|float|string|object $value): ?OwnedList
    {
        return $this->ownedLists[$this->values->indexOf($value)] ?? null;
    }

    /**
     * Get all values shared with input
     *
     * @param int|float|string|object $input
     * @return array
     */
    public function get(int|float|string|object $input): array
    {
        $result = [];

        if ($list = $this->getOwnedList($input)) {
            foreach ($list as $value) {
                $result[] = $value;
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
        foreach ($this->ownedLists as $list) {
            if (count($list) > 0) {
                yield from $list;
            }
        }
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {

        return $this->hasValue($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->add($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        $this->deleteValue($offset);
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        $count = 0;
        foreach ($this->ownedLists as $list) {
            $count += count($list);
        }
        return $count / 2;
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        yield from $this->entries();
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return [];
    }

    /** {@inheritdoc} */
    public function __toString(): string
    {
        return sprintf('[object %s]', static::class);
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {

        $result = [];
        foreach ($this->entries() as $ownedBy => $owned) {
            $result[] = [$ownedBy, $owned];
        }
        return $result;
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->values, $this->ownedLists];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {
        list($this->values, $this->ownedLists) = $data;
    }

}
