<?php

declare(strict_types=1);

namespace NGSOFT\Types;

class Sequence implements Reversible, Collection
{

    public function entries(Sort $sort): iterable
    {
        yield from $sort->is(Sort::ASC) ? $this->getIterator() : $this->getReverseIterator();
    }

    public function offsetExists(mixed $offset): bool
    {

    }

    public function offsetGet(mixed $offset): mixed
    {

    }

    public function offsetSet(mixed $offset, mixed $value): void
    {

    }

    public function offsetUnset(mixed $offset): void
    {

    }

    public function count(): int
    {

    }

    public function getIterator(): \Traversable
    {

    }

    public function getReverseIterator(): \Traversable
    {

    }

}
