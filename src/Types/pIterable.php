<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use IteratorAggregate,
    Traversable;

abstract class pIterable implements IteratorAggregate
{

    abstract protected function __iter__(): iterable;

    final public function getIterator(): Traversable
    {
        yield from $this->__iter__();
    }

}
