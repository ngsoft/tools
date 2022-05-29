<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

final class SharedList
{

    private Set $values;
    private $pairs;

    public function __construct()
    {
        $this->values = Set::create();
    }

}
