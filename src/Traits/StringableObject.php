<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

trait StringableObject
{
    public function __toString(): string
    {
        return sprintf('object(%s)#%d', get_class($this), spl_object_id($this));
    }
}
