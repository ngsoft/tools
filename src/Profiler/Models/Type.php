<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

class Type implements \Stringable
{

    public static function create(string $name)
    {
        return new static($name);
    }

    /**
     * @link https://www.php.net/manual/en/language.types.declarations.php
     * @param string $type
     * @return bool
     */
    public static function isBuiltinType(string $type): bool
    {
        static $builtin = [
            'self', 'parent',
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'void', 'never', 'static', 'null', 'false',
        ];

        return in_array(strtolower($type), $builtin);
    }

    public function __construct(
            public readonly string $name
    )
    {

    }

    public function isInstanciable(): bool
    {
        return is_instanciable($this->name);
    }

    public function isClass(): bool
    {
        return $this->isClassName() || $this->isInterfaceName();
    }

    public function isClassName(): bool
    {
        return class_exists($this->name);
    }

    public function isInterfaceName(): bool
    {
        return interface_exists($this->name);
    }

    public function isNullable(): bool
    {
        return $this->name === 'null' || $this->name === 'mixed';
    }

    public function isBuiltin(): bool
    {
        return self::isBuiltinType($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

}
