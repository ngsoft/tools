<?php

declare(strict_types=1);

namespace NGSOFT\Type;

interface ReversibleIterator extends \IteratorAggregate
{

    public function getReverseIterator(): iterable;

    public function entries(Sort $sort): iterable;
}
