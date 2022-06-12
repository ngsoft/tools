<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    Generator,
    IteratorAggregate,
    JsonSerializable,
    NGSOFT\Traits\StringableObject,
    OutOfRangeException,
    Stringable,
    Traversable;

/**
 * An array with fixed capacity
 * Uses LRU model (Last Recently Used gets removed first)
 * SplFixedArray only works with int offsets (not null or strings)
 *
 */
final class FixedArray implements Countable, IteratorAggregate, ArrayAccess, JsonSerializable, Stringable
{

    use StringableObject;

    public const DEFAULT_CAPACITY = 8;

    private array $storage = [];
    private int $size;

    /**
     * Creates a new Fixed Array
     *
     * @param int $size
     * @return static
     */
    public static function create(int $size = self::DEFAULT_CAPACITY): static
    {
        return new static($size);
    }

    public function __construct(
            int $size = self::DEFAULT_CAPACITY
    )
    {
        $this->setSize($size);
    }

    public function clear(): void
    {
        $this->storage = [];
    }

    /**
     * Gets the size of the array.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Change the size of an array to the new size of size. If size is less than the current array size,
     * any values after the new size will be discarded.
     * If size is greater than the current array size, the array will be padded with null values.
     *
     * @param int $size
     * @return bool
     * @throws OutOfRangeException
     */
    public function setSize(int $size): bool
    {
        if ($size < 1) {
            throw new OutOfRangeException(sprintf('Invalid size int %d < 1', $size));
        }
        $this->size = $size;
        $this->enforceCapacity();
        return true;
    }

    private function getIndexes(): Generator
    {
        foreach (array_keys($this->storage) as $offset) { yield $offset; }
    }

    private function append(int|string|null $key, mixed $value): void
    {
        if (null !== $key) {
            unset($this->storage[$key]);
            $this->storage[$key] = $value;
        } else $this->storage[] = $value;
        $this->enforceCapacity();
    }

    private function enforceCapacity(): void
    {
        foreach ($this->getIndexes() as $offset) {
            if ($this->count() > $this->size) { unset($this->storage[$offset]); } else { break; }
        }
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        foreach ($this->getIndexes() as $offset) { yield $offset => $this->storage[$offset]; }
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return $this->storage;
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    /** {@inheritdoc} */
    public function &offsetGet(mixed $offset): mixed
    {
        if (null === $offset) {
            $this->append($offset, []);
            $offset = array_key_last($this->storage);
        }
        if (array_key_exists($offset, $this->storage)) {
            $value = &$this->storage[$offset];
        } else $value = null;
        return $value;
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->append($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$offset]);
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return $this->storage;
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->size, $this->storage];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {
        list($this->size, $this->storage) = $data;
    }

    /** {@inheritdoc} */
    public function __clone(): void
    {
        $storage = [];
        foreach ($this->storage as $key => $value) {
            if (is_object($value)) $value = clone $value;
            $storage[$key] = $value;
        }
        $this->storage = $storage;
    }

}
