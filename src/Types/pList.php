<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use JsonSerializable,
    Stringable;
use function in_range;

/**
 *
 * @link https://docs.python.org/3/tutorial/datastructures.html
 */
class pList extends pMutableSequence
{

    public function __construct(
            iterable $list = []
    )
    {
        $this->extend($list);
    }

    protected function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    protected function withData(array $data): static
    {
        return $this->copy()->setData($data);
    }

    /**
     * Return a shallow copy of the list
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Insert an item at a given position.
     * The first argument is the index of the element before which to insert, so a.insert(0, x) inserts at the front of the list,
     * and a.insert(len(a), x) is equivalent to a.append(x).
     */
    public function insert(int $offset, mixed $value): void
    {
        $offset = $this->getOffset($offset);

        if ( ! in_range($offset, 0, $this->count())) {
            throw IndexError::for($offset, $this);
        }

        if ($offset === $this->count()) {
            $this->data[] = $value;
            return;
        }
        array_splice($this->data, $offset, 0, $value);
    }

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

        $this->data[$_offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $offset = $this->getOffset($offset);
        $max = $this->count() - 1;
        try {

            if (is_int($offset)) {
                if ( ! in_range($offset, 0, $max)) {
                    parent::offsetUnset($offset);
                }

                unset($this->data[$offset]);
                return;
            }
            foreach ($offset->getIteratorFor($this) as $_offset) {

                if ( ! in_range($_offset, 0, $max)) {
                    parent::offsetUnset($_offset);
                }

                unset($this->data[$offset]);
            }
        } finally {
            $this->data = array_values($this->data);
        }
    }

}
