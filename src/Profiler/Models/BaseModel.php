<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use BadMethodCallException,
    InvalidArgumentException,
    ReflectionClass,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty,
    Stringable;
use function get_debug_type;

abstract class BaseModel implements Stringable
{

    /** @var object[] */
    protected array $attributes = [];

    abstract public static function getReflectorClassName(): string;

    public static function create(mixed $reflector): static
    {
        return new static($reflector);
    }

    public function __construct(protected ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionParameter|ReflectionProperty $reflector)
    {
        if ( ! is_a($reflector, static::getReflectorClassName())) {
            throw new InvalidArgumentException(sprintf('Invalid type %s for $reflector type %s', get_debug_type($reflector), static::getReflectorClassName()));
        }
    }

    public function __call(string $name, array $arguments): mixed
    {

        if (method_exists($this->reflector, $name)) {
            return $this->reflector->{$name}(...$arguments);
        }
        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name));
    }

    public function __toString(): string
    {
        return (string) $this->reflector;
    }

}
