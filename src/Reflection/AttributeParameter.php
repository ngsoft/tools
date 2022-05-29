<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

class AttributeParameter
{

    public function __construct(
            public readonly string $name,
            public readonly string $type
    )
    {

    }

}
