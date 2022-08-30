<?php

declare(strict_types=1);

namespace NGSOFT\Types\Traits;

use NGSOFT\Types\Sort,
    Traversable;

trait IsReversible
{

    public function entries(Sort $sort): iterable
    {
        yield from $sort->is(Sort::ASC) ? $this->getIterator() : $this->getReverseIterator();
    }

    abstract public function getReverseIterator(): Traversable;

    abstract public function getIterator(): Traversable;
}
