<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use OutOfBoundsException;
use function get_debug_type;

class SimpleArray extends Collection
{

    protected function assertValidOffset(mixed $offset): void
    {
        if ( ! is_int($offset) && ! is_null($offset)) {
            throw new OutOfBoundsException(sprintf('%s only accepts offsets of type int|null, %s given.', static::class, get_debug_type($offset)));
        }
    }

    /**
     * Prepend one or more elements to the beginning of an array
     */
    public function unshift(mixed ...$values): int
    {
        $this->assertLocked();

        foreach ($values as $value) {
            if ($value instanceof self) { $value = $value->storage; }
            array_unshift($this->storage, $value);
        }
        if (count($values)) {
            $this->update();
        }
        return $this->count();
    }

    /**
     * Appends one or more elements at the end of an array
     */
    public function push(mixed ...$values): int
    {
        $this->assertLocked();

        foreach ($values as $value) {
            if ($value instanceof self) { $value = $value->storage; }
            array_push($this->storage, $value);
        }

        if (count($values)) {
            $this->update();
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
        $this->assertLocked();

        $value = array_shift($this->storage);
        if (is_array($value) && $this->recursive) {
            $value = new static($value, $this->recursive);
        }
        $this->update();
        return $value;
    }

    /**
     * Pop the element off the end of array
     *
     * @return mixed the removed element
     */
    public function pop(): mixed
    {
        $this->assertLocked();

        $value = array_pop($this->storage);
        if (is_array($value) && $this->recursive) {
            $value = new static($value, $this->recursive);
        }
        $this->update();
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
        $id = array_search($value, $this->storage, true);
        return $id === false ? -1 : $id;
    }

}
