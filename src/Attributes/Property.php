<?php

declare(strict_types=1);

namespace NGSOFT\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Property {

    use AttributeTrait;

    public function __construct(
            public readonly bool $canGet = false,
            public readonly bool $canSet = false,
            public readonly bool $canIsset = false,
            public readonly bool $canUnset = false,
            public readonly bool $serializable = true,
    ) {

    }

}
