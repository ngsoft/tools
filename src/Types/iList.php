<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use Throwable;

/**
 * @link https://docs.python.org/3/tutorial/datastructures.html
 */
class iList extends MutableSequence
{

    protected array $data = [];

    public function __construct(
            iterable $list = []
    )
    {
        $this->extend($list);
    }

    protected function getOffset(Slice|int|string|null $offset): array|int
    {

        if (is_null($offset)) {
            return $this->count();
        }

        if (is_int($offset)) {

            if ($offset < 0) {
                $offset += $this->count();
            }

            return $offset;
        }

        if (is_string($offset)) {
            if ( ! Slice::isValid($offset)) {
                throw IndexError::for($offset, $this);
            }

            $offset = Slice::of($offset);
        }

        return $offset->getOffsetList($this);
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

        if ($offset === -1) {
            $offset = $this->count();
        }
        if ( ! in_range($offset, 0, $this->count())) {
            throw IndexError::for($offset, $this);
        }

        if ($offset === $this->count()) {
            $this->data[] = $offset;
            return;
        }

        array_splice($this->data, $offset, 0, $value);
    }

    public function count(): int
    {
        return count($this->data);
    }

}
