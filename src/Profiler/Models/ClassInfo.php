<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

class ClassInfo extends BaseModel
{

    protected \ReflectionClass $reflector;
    public readonly string $name;

    public function __construct(
            object|string $reflector
    )
    {

        if ( ! $reflector instanceof \ReflectionClass) {
            $reflector = new \ReflectionClass($reflector);
        }
        $this->reflector = $reflector;

        $this->name = $reflector->getName();
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->reflector->{$name}(...$arguments);
    }

}
