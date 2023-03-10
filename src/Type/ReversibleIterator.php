<?php

declare(strict_types=1);

namespace NGSOFT\Type;

use Countable,
    IteratorAggregate,
    Traversable;

interface ReversibleIterator extends IteratorAggregate, Countable
{

    public function getReverseIterator(): Traversable;

    public function entries(Sort $sort = Sort::ASC): iterable;
}
