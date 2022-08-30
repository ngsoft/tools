<?php

declare(strict_types=1);

namespace NGSOFT\Types;

class Sequence implements Reversible
{

    public function entries(Sort $sort): iterable
    {
        yield from $sort->is(Sort::ASC) ? $this->getIterator() : $this->getReverseIterator();
    }

}
