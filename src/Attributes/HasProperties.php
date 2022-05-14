<?php

declare(strict_types=1);

namespace NGSOFT\Attributes;

#[\Attribute]
class HasProperties {

    public function __construct(
            public string $key = 'properties'
    ) {

    }

    public function getKey(): string {
        return $this->key;
    }

}
