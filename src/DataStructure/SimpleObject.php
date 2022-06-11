<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    OutOfBoundsException,
    Stringable;
use function get_debug_type;

class SimpleObject implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Stringable
{

    use ArrayAccessCommon;

    protected string $filename = '';

    /**
     * Instanciates a new instance using the given json file and syncs it (writes to it when keys are added/removed)
     *
     * @param string $filename
     * @param bool $recursive
     * @return static
     * @throws InvalidArgumentException
     */
    public static function syncJsonFile(string $filename, bool $recursive = true): static
    {
        $instance = static::fromJsonFile($filename, $recursive);
        $instance->filename = $filename;
        return $instance;
    }

    /**
     * Write to json file when modified
     * @return void
     */
    protected function update(): void
    {
        if (!empty($this->filename)) {
            $this->saveToJson($this->filename);
        }
    }

    protected function assertValidImport(array $import): void
    {

        foreach (array_keys($import) as $offset) {
            if (!is_int($offset) && !is_string($offset)) {
                throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
            }
        }
    }

    protected function append(mixed $offset, mixed $value): void
    {

        if (null === $offset) {
            $this->storage[] = $value;
            return;
        }

        if (!is_int($offset) && !is_string($offset)) {
            throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
        }

        $this->offsetUnset($offset);
        if ($value instanceof self) $value = $value->storage;
        $this->storage[$offset] = $value;
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful
     *
     * @param mixed $value
     * @return int|string|null
     */
    public function search(mixed $value): int|string|null
    {
        if ($value instanceof self) $value = $value->storage;
        $offset = array_search($value, $this->storage, true);
        return $offset === false ? null : $offset;
    }

    /** {@inheritdoc} */
    public function &__get(string $name): mixed
    {
        $value = $this->offsetGet($name);
        return $value;
    }

    /** {@inheritdoc} */
    public function __set(string $name, mixed $value): void
    {
        $this->offsetSet($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset(string $name): void
    {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->storage, $this->recursive, $this->filename];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data)
    {
        list($this->storage, $this->recursive, $this->filename) = $data;
    }

}
