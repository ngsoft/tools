<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    InvalidArgumentException,
    IteratorAggregate,
    JsonSerializable;
use NGSOFT\{
    Tools, Traits\StringableObject
};
use OutOfBoundsException,
    RuntimeException,
    Stringable,
    Traversable;

/**
 * A base Collection
 */
abstract class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Stringable
{

    use StringableObject;

    protected array $storage = [];
    // Update event
    protected ?self $parent = null;
    protected int|string|null $offset = null;

    /**
     * Create new instance
     */
    public static function create(array $array = [], bool $recursive = false): static
    {
        return new static($array, $recursive);
    }

    /**
     * Instanciates a new instance using the given array
     */
    public static function from(array $array, bool $recursive = true): static
    {
        return new static($array, $recursive);
    }

    /**
     * Instanciates a new instance using the given json
     */
    public static function fromJson(string $json, bool $recursive = true): static
    {
        $array = json_decode($json, true);
        if (
                (json_last_error() === JSON_ERROR_NONE) && is_array($array)
        ) { return static::from($array, $recursive); }
        throw new InvalidArgumentException("Cannot import: Invalid JSON");
    }

    /**
     * Instanciates a new instance using the given file
     */
    public static function fromJsonFile(string $filename, bool $recursive = true): static
    {
        if (is_file($filename)) {
            $string = file_get_contents($filename);
            if (false !== $string) { return static::fromJson($string, $recursive); }
        }
        throw new InvalidArgumentException("Cannot import {$filename}: Invalid json file");
    }

    public function __construct(
            array $array = [],
            protected bool $recursive = false
    )
    {
        $this->assertValidImport($array);
        $this->storage = $array;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->storage);
    }

    public function &offsetGet(mixed $offset): mixed
    {

        if (is_null($offset)) {
            if ( ! $this->recursive) {
                throw new OutOfBoundsException('Cannot overload object ($object[][]) if it is not recursive.');
            }
            $value = $this->prepForUpdate($offset);

            return $value;
        }

        if ( ! array_key_exists($offset, $this->storage)) {
            $value = null;
        } elseif (is_array($this->storage[$offset])) {

            if ($this->recursive) {
                $value = $this->prepForUpdate($offset);
            } else { $value = &$this->storage[$offset]; }
        } else { $value = $this->storage[$offset]; }

        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->append($offset, $value);

        $this->update();
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$offset]);
        $this->update();
    }

    public function count(): int
    {
        return count($this->storage);
    }

    public function getIterator(): Traversable
    {
        yield from $this->entries();
    }

    /**
     * Checks if import is valid
     */
    abstract protected function assertValidImport(array $array): void;

    /**
     * Appends a value at the end of the array updating the internal pointer
     * @return int|string current offset
     */
    abstract protected function append(mixed $offset, mixed $value): int|string;

    protected function prepForUpdate(mixed $offset): static
    {

        $instance = $this->getNewInstance(true);
        $instance->offset = $offset;
        if ( ! is_null($offset)) {
            $instance->storage = $this->storage[$offset];
        }
        return $instance;
    }

    /**
     * Update data on dynamic object creation
     */
    protected function update(?self $child = null): void
    {
        if ($child instanceof static) {
            $child->offset = $this->append($child->offset, $child->storage);
        }

        $this->parent?->update($this);
    }

    protected function getNewInstance(bool $parent = false): static
    {
        $instance = new static(recursive: $this->recursive);
        if ($parent) { $instance->parent = $this; }
        return $instance;
    }

    /**
     * Exports to json
     */
    public function toJson(int $flags = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    /**
     * Saves Contents to json file
     */
    public function saveToJson(string $file): bool
    {

        if (file_exists($file) && ! is_file($file)) {
            throw new RuntimeException(sprintf('Cannot save directory "%s" as json file.', $file));
        }

        $dir = dirname($file);
        if (is_dir($dir) || mkdir($dir, 0777, true)) {
            return file_put_contents($file, $this->toJson(), LOCK_EX) > 0;
        }
        return false;
    }

    /**
     * Exports to array
     */
    public function toArray(): array
    {
        return $this->storage;
    }

    /**
     * Clears the Storage
     *
     * @return void
     */
    public function clear(): void
    {
        $array = [];
        $this->storage = &$array;
    }

    /**
     * Returns a new iterable indexed by id
     */
    public function entries(Sort $sort = Sort::ASC): iterable
    {

        foreach ($this->keys($sort) as $offset) {
            yield $offset => $this->offsetGet($offset);
        }
    }

    /**
     * Returns a new iterable with only the values
     */
    public function values(Sort $sort = Sort::ASC): iterable
    {
        foreach ($this->keys($sort) as $offset) { yield $this->offsetGet($offset); }
    }

    /**
     * Returns a new iterable with only the indexes
     * @return iterable<string|int>
     */
    public function keys(Sort $sort = Sort::ASC): iterable
    {
        $indexes = array_keys($this->storage);

        if ($sort->is(Sort::DESC)) {
            $indexes = array_reverse($indexes);
        }
        foreach ($indexes as $offset) { yield $offset; }
    }

    /**
     * Applies the callback to the elements of the storage and returns a copy
     */
    public function map(callable $callback): static
    {
        $result = $this->getNewInstance();

        foreach ($this->entries() as $offset => $value) {

            $newValue = $callback($value, $offset, $this);

            if ($newValue === null) {
                $newValue = $value;
            }

            $result->offsetSet(is_string($offset) ? $offset : null, $newValue);
        }

        return $result;
    }

    /**
     * Returns a copy with all the elements that passes the test
     */
    public function filter(callable $callback): static
    {
        $result = $this->getNewInstance();
        foreach ($this->entries() as $offset => $value) {

            if ( ! $callback($value, $offset, $this)) {
                continue;
            }
            if ( ! is_string($offset)) {
                $offset = null;
            }

            $result->offsetSet($offset, $value);
        }

        return $result;
    }

    /**
     * Tests if at least one of the elements from the storage pass the test implemented by the callable
     */
    public function some(callable $callback): bool
    {
        return Tools::some($callback, $this->entries());
    }

    /**
     * Tests if all the elements from the storage pass the test implemented by the callable
     */
    public function every(callable $callback): bool
    {
        return Tools::every($callback, $this->entries());
    }

    /**
     * Runs the given callable for each of the elements
     */
    public function each(callable $callback): void
    {
        Tools::each($callback, $this->entries());
    }

    protected function clone(array $array, bool $recursive): array
    {

        foreach ($array as $offset => $value) {

            if (is_object($value)) {
                $array[$offset] = clone $value;
            }


            if (is_array($value) && $recursive) {
                $array[$offset] = $this->clone($value, $recursive);
            }
        }

        return $array;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function __clone(): void
    {
        $this->storage = $this->clone($this->storage, $this->recursive);
    }

    public function __serialize(): array
    {
        return [$this->storage, $this->recursive];
    }

    public function __unserialize(array $data): void
    {
        list($this->storage, $this->recursive) = $data;
    }

    public function __debugInfo(): array
    {
        return $this->storage;
    }

}
