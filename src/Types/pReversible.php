<?php

declare(strict_types=1);

namespace NGSOFT\Types;

/**
 * Python like Reversible
 */
interface pReversible extends pIterable
{

    /**
     * Iterate in reverse
     */
    public function getReverseIterator(): \Traversable;

    /**
     * Access Entries with Sorting method
     */
    public function entries(Sort $sort): iterable;
}
