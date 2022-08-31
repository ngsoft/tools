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

    /** {@inheritdoc} */
    public function insert(int $offset, mixed $value): void
    {
        $offset = $this->getOffset($offset);

        if ( ! in_range($offset, 0, $this->count())) {
            throw IndexError::for($offset, $this);
        }

        if ($offset === $this->count()) {
            $this->data[] = $value;
        } else { array_splice($this->data, $offset, 0, $value); }
    }

    protected function __len__(): int
    {
        return count($this->data);
    }

    protected function __delitem__(int $offset): void
    {
        unset($this->data[$offset]);
    }

    protected function __setitem__(int $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    protected function __getitem__(int $offset): mixed
    {
        return $this->data[$offset];
    }

}
