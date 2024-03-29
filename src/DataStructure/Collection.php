<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Tools;
use NGSOFT\Traits\ObjectLock;
use NGSOFT\Traits\ReversibleIteratorTrait;
use NGSOFT\Traits\StringableObject;

/**
 * A base Collection.
 */
abstract class Collection implements \ArrayAccess, ReversibleIterator, \JsonSerializable, \Stringable
{
    use StringableObject;

    use CommonMethods;

    use ObjectLock;

    use ReversibleIteratorTrait;

    protected array $storage = [];
    protected ?self $parent  = null;

    public function __construct(
        array $array = [],
        protected bool $recursive = false
    ) {
        $this->assertValidImport($array);
        $this->storage = $array;
    }

    public function __clone(): void
    {
        $this->storage = $this->cloneArray($this->storage);
    }

    public function __serialize(): array
    {
        return [$this->storage, $this->recursive, $this->locked];
    }

    public function __unserialize(array $data): void
    {
        list($this->storage, $this->recursive, $this->locked) = $data;
    }

    public function __debugInfo(): array
    {
        return iterator_to_array($this);
    }

    /**
     * Create new instance.
     */
    public static function create(array $array = [], bool $recursive = false): static
    {
        return new static($array, $recursive);
    }

    /**
     * Instanciates a new instance using the given array.
     */
    public static function from(array $array, bool $recursive = false): static
    {
        return new static($array, $recursive);
    }

    /**
     * Instanciates a new instance using the given json.
     */
    public static function fromJson(string $json, bool $recursive = true): static
    {
        if (is_array($array = json_decode($json, true, flags: JSON_THROW_ON_ERROR)))
        {
            return static::from($array, $recursive);
        }
        throw new \InvalidArgumentException(sprintf('Invalid json return type, array expected, %s given.', \get_debug_type($array)));
    }

    /**
     * Instanciates a new instance using the given file.
     */
    public static function fromJsonFile(string $filename, bool $recursive = true): static
    {
        if (is_file($filename))
        {
            $string = file_get_contents($filename);

            if (false !== $string)
            {
                return static::fromJson($string, $recursive);
            }
        }
        throw new \InvalidArgumentException("Cannot import {$filename}: file does not exists or cannot be read.");
    }

    public function count(): int
    {
        $this->reload();
        return count($this->storage);
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->reload();
        return null !== $offset && array_key_exists($offset, $this->storage);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->assertValidOffset($offset);

        if ( ! $this->offsetExists($offset))
        {
            $null = null;
            return $null;
        }

        if (is_array($this->storage[$offset]) && $this->recursive)
        {
            $instance          = $this->getNewInstance($this);
            $instance->storage = &$this->storage[$offset];
            return $instance;
        }

        $value = &$this->storage[$offset];
        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->isLocked())
        {
            return;
        }

        try
        {
            $this->reload();
            $this->append($offset, $value);
        } finally
        {
            $this->update();
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($this->isLocked())
        {
            return;
        }

        try
        {
            $this->reload();
            unset($this->storage[$offset]);
        } finally
        {
            $this->update();
        }
    }

    /**
     * Appends a value at the end of the array updating the internal pointer.
     *
     * @return int|string current offset
     */
    public function append(mixed $offset, mixed $value): int|string
    {
        $this->assertValidOffset($offset);

        if ($value instanceof self)
        {
            $value = $value->storage;
        }

        $this->assertValidValue($value);

        if (null === $offset)
        {
            $this->storage[] = $value;
            return array_key_last($this->storage);
        }

        unset($this->storage[$offset]);
        $this->storage[$offset] = $value;

        return $offset;
    }

    /**
     * Exports to json.
     */
    public function toJson(int $flags = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    /**
     * Saves Contents to json file.
     */
    public function saveToJson(string $file): bool
    {
        if (file_exists($file) && ! is_file($file))
        {
            throw new \RuntimeException(sprintf('Cannot save directory "%s" as json file.', $file));
        }

        $dir = dirname($file);

        if (is_dir($dir) || mkdir($dir, 0777, true))
        {
            return file_put_contents($file, $this->toJson(), LOCK_EX) > 0;
        }
        return false;
    }

    /**
     * Returns a new iterable indexed by id.
     */
    public function entries(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->keys($sort) as $offset)
        {
            yield $offset => $this->offsetGet($offset);
        }
    }

    /**
     * Returns a new iterable with only the values.
     */
    public function values(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->keys($sort) as $offset)
        {
            yield $this->offsetGet($offset);
        }
    }

    /**
     * Returns a new iterable with only the indexes.
     *
     * @return iterable<int|string>
     */
    public function keys(Sort $sort = Sort::ASC): iterable
    {
        yield from $this->sortArray(array_keys($this->storage), $sort);
    }

    /**
     * Applies the callback to the elements of the storage and returns a copy.
     */
    public function map(callable $callback): static
    {
        $result = $this->getNewInstance();

        foreach ($this->entries() as $offset => $value)
        {
            $newValue = $callback($value, $offset, $this);

            if (null === $newValue)
            {
                $newValue = $value;
            }

            if ( ! is_string($offset))
            {
                $offset = null;
            }

            $result->offsetSet($offset, $newValue);
        }

        return $result;
    }

    /**
     * Returns a copy with all the elements that passes the test.
     */
    public function filter(callable $callback): static
    {
        $result = $this->getNewInstance();

        foreach ($this->entries() as $offset => $value)
        {
            if ( ! $callback($value, $offset, $this))
            {
                continue;
            }

            if ( ! is_string($offset))
            {
                $offset = null;
            }

            $result->offsetSet($offset, $value);
        }

        return $result;
    }

    /**
     * Checks if value in the storage.
     */
    public function has(mixed $value): bool
    {
        if ($value instanceof self)
        {
            $value = $value->storage;
        }
        return in_array($value, $this->storage, true);
    }

    /**
     * Pull the value(s) from the storage and returns it.
     */
    public function pull(iterable|int|string ...$keys): mixed
    {
        return Tools::pull($keys, $this);
    }

    /**
     * Concatenate multiple values into the storage.
     */
    public function concat(mixed ...$values): static
    {
        Tools::concat($this, ...$values);
        return $this;
    }

    /**
     * Checks if a value is a collection with the same items as current.
     */
    public function equals(mixed $value): bool
    {
        if (false === $value instanceof self)
        {
            return false;
        }

        return $value->toArray() === $this->toArray();
    }

    /**
     * Exports to array.
     */
    public function toArray(): array
    {
        return $this->storage;
    }

    /**
     * Clears the Storage.
     */
    public function clear(): void
    {
        if ($this->isLocked())
        {
            return;
        }
        $array         = [];
        $this->storage = &$array;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Gets called before every transaction (isset, get, set, unset).
     */
    protected function reload(): void
    {
        $this->parent?->reload();
    }

    /**
     * Get called if data has been changed.
     */
    protected function update(): void
    {
        $this->parent?->update();
    }

    /**
     * Checks if import is valid.
     */
    protected function assertValidImport(array $array): void
    {
        foreach (array_keys($array) as $offset)
        {
            $this->assertValidOffset($offset);

            $this->assertValidValue($array[$offset]);

            if ($this->recursive && is_array($array[$offset]))
            {
                $this->assertValidImport($array[$offset]);
            }
        }
    }

    /**
     * Checks if the offset is valid.
     */
    protected function assertValidOffset(mixed $offset): void
    {
        if ( ! is_int($offset) && ! is_string($offset) && ! is_null($offset))
        {
            throw new \OutOfBoundsException(sprintf('%s only accepts offsets of type string|int|null, %s given.', $this, \get_debug_type($offset)));
        }
    }

    /**
     * Checks if value is valid.
     */
    protected function assertValidValue(mixed $value): void
    {
        // accepts anything, override this to set your conditions
        if ( ! is_scalar($value) && ! is_array($value) && ! is_object($value) && ! is_null($value))
        {
            throw new \ValueError(sprintf('%s can only use types string|int|float|bool|null|array|object, %s given.', $this, \get_debug_type($value)));
        }
    }

    /**
     * Creates new instance copying properties and binding parent if needed.
     */
    protected function getNewInstance(?self $parent = null): static
    {
        $instance         = static::create(recursive: $this->recursive);
        $instance->parent = $parent;

        return $instance;
    }
}
