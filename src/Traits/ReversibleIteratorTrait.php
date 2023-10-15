<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use NGSOFT\DataStructure\Sort;

trait ReversibleIteratorTrait
{
    abstract public function entries(Sort $sort = Sort::ASC): iterable;

    public function getIterator(): \Traversable
    {
        yield from $this->entries();
    }

    public function getReverseIterator(): \Traversable
    {
        yield from $this->entries(Sort::DESC);
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }
}
