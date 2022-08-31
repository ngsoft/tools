<?php

declare(strict_types=1);

namespace NGSOFT\Types;

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
