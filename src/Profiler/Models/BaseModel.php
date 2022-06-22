<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use BadMethodCallException;

class BaseModel
{

    public static function create(mixed $reflector): static
    {
        return new static($reflector);
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ( ! isset($this->reflector) || ! method_exists($this->reflector, $name)) {

            throw new BadMethodCallException(sprintf('%s::%s() does not exists', static::class, $name));
        }



        return $this->reflector->{$name}(...$arguments);
    }

}
