<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use BadMethodCallException;

abstract class BaseModel implements \Stringable
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

    public function __toString(): string
    {

        if (isset($this->reflector)) {
            return (string) $this->reflector;
        }


        return sprintf('object(%s)#%d', get_class($this), spl_object_id($this));
    }

}
