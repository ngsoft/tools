<?php

declare(strict_types=1);

namespace NGSOFT\Types;

class iList extends MutableSequence
{

    protected array $list = [];

    public function __construct(
            iterable $list = []
    )
    {
        $this->extend($list);
    }

}
