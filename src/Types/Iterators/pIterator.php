<?php

declare(strict_types=1);

namespace NGSOFT\Types\Iterators;

/**
 * Basic Iterator Proxy
 */
class pIterator implements \NGSOFT\Types\pIterable
{

    public function __construct(
            protected iterable $iterator
    )
    {

    }

    public function getIterator(): \Traversable
    {

    }

    public function entries(\NGSOFT\Types\Sort $sort): iterable
    {

    }

}
