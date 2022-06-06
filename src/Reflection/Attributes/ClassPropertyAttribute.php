<?php

declare(strict_types=1);

namespace NGSOFT\Reflection\Attributes;

class ClassPropertyAttribute extends ClassAttribute
{

    public readonly string $propertyName;

    public function __construct(\ReflectionProperty $reflector, object $attribute)
    {
        $this->propertyName = $reflector->getName();
        parent::__construct($reflector->getDeclaringClass(), $attribute);
    }

}
