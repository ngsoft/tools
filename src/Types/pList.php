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
class pList extends MutableSequence implements JsonSerializable, Stringable
{

    public function __construct(
            iterable $list = []
    )
    {
        $this->extend($list);
    }

    protected function getOffset(Slice|int|string|null $offset): Slice|int
    {

        if (is_null($offset)) {
            return $this->count();
        }
        if (is_string($offset) && ! Slice::isValid($offset)) {
            throw IndexError::for($offset, $this);
        }


        if (is_int($offset) && $offset < 0) {
            $offset += $this->count();

            if ($offset === -1 && ! $this->count()) {
                $offset = 0;
            }
        } elseif (is_string($offset)) {
            $offset = Slice::of($offset);
        }

        return $offset;
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

    public function count(): int
    {
        return count($this->data);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {

        if ( ! is_int($offset) && ! is_null($offset)) {
            parent::offsetSet($offset, $value);
        }

        if (is_int($offset)) {

            $_offset = $this->getOffset($offset);

            if ( ! in_range($_offset, 0, $this->count() - 1)) {
                parent::offsetSet($offset, $value);
            }
        } elseif (is_null($offset)) {
            $_offset = $this->count();
        }

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

    public function offsetGet(mixed $offset): mixed
    {
        if ( ! $this->count()) {
            parent::offsetGet($offset);
        }


        $offset = $this->getOffset($offset);

        if (is_int($offset)) {
            if ( ! in_range($offset, 0, $this->count() - 1)) {
                parent::offsetGet($offset);
            }

            return $this->data[$offset];
        }

        return $this->withData($offset->slice($this));
    }

    public function __serialize(): array
    {
        return [$this->data];
    }

    public function __unserialize(array $data)
    {
        [$this->data] = $data;
    }

    public function __debugInfo(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    public function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

}
