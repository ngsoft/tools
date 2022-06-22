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

    public function __construct(protected \Reflector $reflector)
    {

    }

    public function __call(string $name, array $arguments): mixed
    {
        if ( ! method_exists($this->reflector, $name)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name));
        }

        return $this->reflector->{$name}(...$arguments);
    }

    public function __toString(): string
    {
        return (string) $this->reflector;
    }

}
