<?php

declare(strict_types=1);

namespace NGSOFT\Reflection\Attributes;

class ClassAttribute
{

    public readonly string $className;
    public object $attribute;

    public function __construct(\ReflectionClass $reflector, object $attribute)
    {
        $this->className = $reflector->getName();
    }

}
