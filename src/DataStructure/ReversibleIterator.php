<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable;

interface ReversibleIterator extends \IteratorAggregate, Countable
{
    public function getReverseIterator(): \Traversable;

    /**
     * Iterates entries in sort order.
     */
    public function entries(Sort $sort = Sort::ASC): iterable;
}
