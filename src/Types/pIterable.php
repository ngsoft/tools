<?php

declare(strict_types=1);

namespace NGSOFT\Types;

interface pIterable extends \IteratorAggregate
{

    /**
     * Access Entries with Sorting method
     */
    public function entries(Sort $sort): iterable;
}
