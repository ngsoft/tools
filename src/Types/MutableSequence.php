<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use Throwable;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter, PhanUnusedPublicNoOverrideMethodParameter
 */
abstract class MutableSequence extends Sequence
{

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw IndexError::for($offset, $this);
    }

    public function offsetUnset(mixed $offset): void
    {
        throw IndexError::for($offset, $this);
    }

    /**
     * insert value before offset
     */
    public function insert(int $offset, mixed $value): void
    {
        throw IndexError::for($offset, $this);
    }

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
            [$this[$i], $this[$len - $i - 1]] = [$this[$len - $i - 1], $this[$i]];
        }
    }

    /**
     * extend sequence by appending elements from the iterable
     */
    public function extend(iterable $values): void
    {

        if ($values === $this) {
            $values = new iList($this);
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
        unset($this[$offset]);
        return $value;
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
