<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use Throwable;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter, PhanUnusedPublicNoOverrideMethodParameter
 */
abstract class pMutableSequence extends pSequence
{

    abstract protected function __setitem__(int $offset, mixed $value): void;

    abstract protected function __delitem__(int $offset): void;

    public function offsetSet(mixed $offset, mixed $value): void
    {

        if ( ! is_int($offset) && ! is_null($offset)) {
            throw IndexError::for($offset, $this);
        }

        if (is_int($offset)) {

            $_offset = $this->getOffset($offset);

            if ( ! in_range($_offset, 0, $this->count() - 1)) {
                throw IndexError::for($offset, $this);
            }
        } else { $_offset = $this->count(); }

        $this->__setitem__($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $offset = $this->getOffset($offset);
        $max = $this->count() - 1;
        try {

            if (is_int($offset)) {
                if ( ! in_range($offset, 0, $max)) {
                    throw IndexError::for($offset, $this);
                }

                $this->__delitem__($offset);
                return;
            }
            foreach ($offset->getIteratorFor($this) as $_offset) {

                if ( ! in_range($_offset, 0, $max)) {
                    throw IndexError::for($_offset, $this);
                }

                $this->__delitem__($offset);
            }
        } finally {
            $this->data = array_values($this->data);
        }
    }

    /**
     * Insert an item at a given position.
     * The first argument is the index of the element before which to insert, so a.insert(0, x) inserts at the front of the list,
     * and a.insert(len(a), x) is equivalent to a.append(x).
     */
    abstract public function insert(int $offset, mixed $value): void;

    /**
     * Append value to the end of the sequence
     */
    public function append(mixed $value): void
    {
        $this->insert($this->count(), $value);
    }

    /**
     * Remove all items
     */
    public function clear(): void
    {
        try {

            while ($this->count()) {
                $this->pop();
            }
        } catch (Throwable) {

        }
    }

    /**
     * reverse *IN PLACE*'
     */
    public function reverse(): void
    {

        $len = $this->count();
        foreach (Range::create((int) floor($len / 2)) as $i) {
            [$this->data[$i], $this->data[$len - $i - 1]] = [$this->data[$len - $i - 1], $this->data[$i]];
        }
    }

    /**
     * extend sequence by appending elements from the iterable
     */
    public function extend(iterable $values): void
    {

        if ($values instanceof self) {
            $values = $values->data;
        }

        foreach ($values as $value) {
            $this->append($value);
        }
    }

    /**
     * remove and return item at index (default last).
     * Raise IndexError if list is empty or index is out of range.
     */
    public function pop(int $offset = -1): mixed
    {
        $value = $this[$offset];
        unset($this->data[$offset]);
        return $value;
    }

    /**
     * remove first occurrence of value.
     * Raise ValueError if the value is not present.
     */
    public function remove(mixed $value): void
    {
        $offset = $this->index($value);
        unset($this->data[$offset]);
    }

}
