<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use BadMethodCallException;

abstract class BaseModel implements \Stringable
{

    /** @var object[] */
    protected array $attributes = [];

    abstract public static function getReflectorClassName(): string;

    public static function create(mixed $reflector): static
    {
        return new static($reflector);
    }

    /**
     * @link https://www.php.net/manual/en/language.types.declarations.php
     * @param string $type
     * @return bool
     */
    protected static function isBuiltinType(string $type): bool
    {
        static $builtin = [
            'self', 'parent',
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'void', 'never', 'static', 'null', 'false',
        ];

        return in_array(strtolower($type), $builtin);
    }

    public function __construct(protected \Reflector $reflector)
    {

        if ( ! is_a($reflector, static::getReflectorClassName())) {
            throw new \InvalidArgumentException(sprintf('Invalid type %s for $reflector type %s', get_debug_type($reflector), static::getReflectorClassName()));
        }
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
