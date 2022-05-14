<?php

declare(strict_types=1);

namespace NGSOFT\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class HasProperties {

    public function __construct(
            public string $key = 'properties',
            public bool $lazy = true,
            public bool $silent = false
    ) {

    }

}
