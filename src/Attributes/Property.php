<?php

declare(strict_types=1);

namespace NGSOFT\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Property {

    use AttributeTrait;

    public function __construct(
            public readonly bool $readable = false,
            public readonly bool $writable = false,
            public readonly bool $serializable = true,
    ) {

    }

}
