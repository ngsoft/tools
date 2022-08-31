<?php

declare(strict_types=1);

namespace NGSOFT\Types;

/**
 * Python like Reversible
 */
abstract class pReversible extends pIterable
{

    /**
     * Iterate in reverse
     */
    abstract protected function __reversed__(): iterable;

    /**
     * Access Entries with Sorting method
     */
    public function entries(Sort $sort): iterable
    {
        yield from $sort->is(Sort::ASC) ? $this->getIterator() : $this->getReverseIterator();
    }

}
