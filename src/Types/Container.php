<?php

declare(strict_types=1);

namespace NGSOFT\Types;

trait Container
{

    /**
     * Checks if value exists
     */
    abstract protected function __contains__(mixed $value): bool;

    /**
     * Checks if collection has value
     */
    public function contains(mixed $value): bool
    {
        return $this->__contains__($value);
    }

}
