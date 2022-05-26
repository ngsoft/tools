<?php

declare(strict_types=1);

namespace NGSOFT\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Property {

    use AttributeTrait;

    /**
     * Magic Method attribute metadata for
     * trait NGSOFT\Traits\PropertyAttributeAccess
     *
     * @param bool $readable Can read property __get()
     * @param bool $writable Can write property __set()
     * @param bool $removable can unset property __unset()
     * @param bool $serializable can serialize property __serialize()
     */
    public function __construct(
            public readonly bool $readable = false,
            public readonly bool $writable = false,
            public readonly bool $removable = false,
            public readonly bool $serializable = false,
    ) {

    }

}
