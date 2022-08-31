<?php

declare(strict_types=1);

namespace NGSOFT\Types;

trait Sized
{

    abstract protected function __len__(): int;

    public function count(): int
    {
        return $this->__len__();
    }

}
