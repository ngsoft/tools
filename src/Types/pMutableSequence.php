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

    /**
     * Insert an item at a given position.
     * The first argument is the index of the element before which to insert, so a.insert(0, x) inserts at the front of the list,
     * and a.insert(len(a), x) is equivalent to a.append(x).
     */
    abstract public function insert(int $offset, mixed $value): void;

    public function offsetSet(mixed $offset, mixed $value): void
    {

        try {
            if (is_null($offset)) {
                $this->__setitem__($this->__len__(), $this->getValue($value));
                return;
            }

            $offset = $this->getOffset($offset);
            $max = $this->__len__() - 1;

            if (is_int($offset = $this->getOffset($offset))) {
                $offsets = [$offset];
            } else { $offsets = $offset->getIteratorFor($this); }

            foreach ($offsets as $_offset) {
                if ( ! in_range($offset, 0, $max)) {
                    throw IndexError::for($offset, $this);
                }
                $this->__setitem__($offset, $this->getValue($value));
            }
        } finally {
            $this->data = array_values($this->data);
        }
    }

    public function offsetUnset(mixed $offset): void
    {

        try {

            $offset = $this->getOffset($offset);
            $max = $this->__len__() - 1;

            if (is_int($offset = $this->getOffset($offset))) {
                $offsets = [$offset];
            } else { $offsets = $offset->getIteratorFor($this); }

            foreach ($offsets as $_offset) {

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
     * Append value to the end of the sequence
     */
    public function append(mixed $value): void
    {
        $this->insert($this->__len__(), $value);
    }

    /**
     * Remove all items
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * reverse *IN PLACE*'
     */
    public function reverse(): void
    {
        $this->data = array_reverse($this->data);
    }

    /**
     * extend sequence by appending elements from the iterable
     */
    public function extend(iterable $values): void
    {
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
        try {
            return $this[$offset];
        } finally {
            unset($this[$offset]);
        }
    }

    /**
     * remove first occurrence of value.
     * Raise ValueError if the value is not present.
     */
    public function remove(mixed $value): void
    {
        unset($this[$this->index($value)]);
    }

}
