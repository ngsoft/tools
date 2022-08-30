<?php

declare(strict_types=1);

namespace NGSOFT\Types;

/**
 * Python like Reversible
 */
interface Reversible extends \IteratorAggregate
{

    /**
     * Access Entries with Sorting method
     */
    public function entries(Sort $sort): iterable;

    /**
     * Iterate in reverse
     */
    public function getReverseIterator(): \Traversable;
}
