<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

class stdObjectIterator extends \ArrayIterator {

    public function current() {
        $value = parent::current();
        $value = is_array($value) ? stdObject::from($value) : $value;
        return $value;
    }

}
