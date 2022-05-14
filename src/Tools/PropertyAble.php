<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

#[\NGSOFT\Attributes\HasProperties]
class PropertyAble {

    /** @var Property[] */
    private $properties = [];

    protected function defineProperty(
            string $name,
            mixed $value,
            bool $enumerable = false,
            bool $configurable = true,
            bool $writable = true
    ) {

    }

}
