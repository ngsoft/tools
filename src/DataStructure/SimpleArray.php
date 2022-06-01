<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Generator,
    OutOfBoundsException,
    Traversable;
use function get_debug_type;

class SimpleArray extends ArrayAccessCommon
{

    public static function create(array $array = [], bool $recursive = false): static
    {
        return new static($array, $recursive);
    }

    protected function assertValidImport(array $import): void
    {

        foreach (array_keys($import) as $offset) {
            if (!is_int($offset)) {
                throw new OutOfBoundsException(sprintf('%s only accepts offsets of type int, %s given.', static::class, get_debug_type($offset)));
            }
        }
    }

    protected function append(mixed $offset, mixed $value): void
    {

        if (null === $offset) {
            $this->push($value);
            return;
        }

        if (!is_int($offset)) {
            throw new OutOfBoundsException(sprintf('%s only accepts offsets of type int, %s given.', static::class, get_debug_type($offset)));
        }

        $this->offsetUnset($offset);
        if ($value instanceof self) $value = $value->storage;
        $this->storage[$offset] = $value;
    }

    /**
     * Prepend one or more elements to the beginning of an array
     *
     * @param mixed $values
     * @return int
     */
    public function unshift(mixed $values): int
    {
        foreach ($values as $value) {
            if ($value instanceof self) $value = $value->storage;
            array_unshift($this->storage, $value);
        }
        return $this->count();
    }

    /**
     *
     * @param mixed $values
     * @return int
     */
    public function push(mixed ...$values): int
    {
        foreach ($values as $value) {
            if ($value instanceof self) $value = $value->storage;
            array_push($this->storage, $value);
        }
        return $this->count();
    }

    /**
     * Shift an element off the beginning of array
     *
     * @return mixed the removed element
     */
    public function shift(): mixed
    {
        $value = array_shift($this->storage);
        if (is_array($value) && $this->recursive) {
            $value = new static($value, $this->recursive);
        }
        return $value;
    }

    /**
     * Pop the element off the end of array
     *
     * @return mixed the removed element
     */
    public function pop(): mixed
    {
        $value = array_pop($this->storage);
        if (is_array($value) && $this->recursive) {
            $value = new static($value, $this->recursive);
        }
        return $value;
    }

    /**
     * Returns the value index
     * @param mixed $value
     * @return int the index or -1 if not found
     */
    public function indexOf(mixed $value): int
    {
        if ($value instanceof self) {
            $value = $value->storage;
        }
        $id = array_search($value, $this->storage);
        return $id === false ? -1 : $id;
    }

}
