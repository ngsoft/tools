<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Traits\ObjectLock;
use NGSOFT\Traits\ReversibleIteratorTrait;
use NGSOFT\Traits\StringableObject;

/**
 * An array with fixed capacity
 * Uses LRU model (Last Recently Used gets removed first)
 * SplFixedArray only works with int offsets (not null or strings).
 */
final class FixedArray implements ReversibleIterator, \ArrayAccess, \JsonSerializable, \Stringable
{
    use StringableObject;

    use CommonMethods;

    use ObjectLock;

    use ReversibleIteratorTrait;

    public const DEFAULT_CAPACITY = 8;

    protected array $storage      = [];
    protected int $size;

    public function __construct(
        int $size = self::DEFAULT_CAPACITY
    ) {
        $this->setSize($size);
    }

    public function __debugInfo(): array
    {
        return $this->storage;
    }

    public function __serialize(): array
    {
        return [$this->size, $this->storage];
    }

    public function __unserialize(array $data): void
    {
        list($this->size, $this->storage) = $data;
    }

    public function __clone(): void
    {
        $storage       = [];

        foreach ($this->storage as $key => $value)
        {
            if (is_object($value))
            {
                $value = clone $value;
            }
            $storage[$key] = $value;
        }
        $this->storage = $storage;
    }

    /**
     * Creates a new Fixed Array.
     */
    public static function create(int $size = self::DEFAULT_CAPACITY): static
    {
        return new self($size);
    }

    public function clear(): void
    {
        $this->storage = [];
    }

    /**
     * Gets the size of the array.
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
     * @throws \OutOfRangeException
     */
    public function setSize(int $size): bool
    {
        if ($size < 1)
        {
            throw new \OutOfRangeException(sprintf('Invalid size int %d < 1', $size));
        }
        $this->size = $size;
        $this->enforceCapacity();
        return true;
    }

    public function count(): int
    {
        return count($this->storage);
    }

    public function entries(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->keys($sort) as $offset)
        {
            yield $offset => $this->storage[$offset];
        }
    }

    /**
     * Returns a new iterable with only the indexes.
     */
    public function keys(Sort $sort = Sort::ASC): iterable
    {
        return $this->sortArray(array_keys($this->storage), $sort);
    }

    /**
     * Returns a new iterable with only the values.
     */
    public function values(Sort $sort = Sort::ASC): iterable
    {
        return $this->sortArray(array_values($this->storage), $sort);
    }

    public function jsonSerialize(): mixed
    {
        return $this->storage;
    }

    public function offsetExists(mixed $offset): bool
    {
        return null !== $this->offsetGet($offset);
    }

    public function &offsetGet(mixed $offset): mixed
    {
        if (null === $offset)
        {
            $this->assertLocked();

            $this->append($offset, []);
            $offset = array_key_last($this->storage);
        }

        if (array_key_exists($offset, $this->storage))
        {
            $value = &$this->storage[$offset];
        } else
        {
            $value = null;
        }
        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->isLocked())
        {
            return;
        }
        $this->append($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($this->isLocked())
        {
            return;
        }
        unset($this->storage[$offset]);
    }

    protected function append(int|string|null $key, mixed $value): void
    {
        if (null !== $key)
        {
            unset($this->storage[$key]);
            $this->storage[$key] = $value;
        } else
        {
            $this->storage[] = $value;
        }
        $this->enforceCapacity();
    }

    protected function enforceCapacity(): void
    {
        foreach ($this->keys() as $offset)
        {
            if ($this->count() > $this->size)
            {
                unset($this->storage[$offset]);
            } else
            {
                break;
            }
        }
    }
}
