<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

class BaseModel
{

    public function create(mixed $reflector): static
    {
        return new static($reflector);
    }

}
