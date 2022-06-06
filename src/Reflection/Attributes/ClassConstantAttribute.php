<?php

declare(strict_types=1);

namespace NGSOFT\Reflection\Attributes;

class ClassConstantAttribute extends ClassAttribute
{

    public readonly string $constantName;

    public function __construct(\ReflectionClassConstant $reflector, object $attribute)
    {
        $this->constantName = $reflector->getName();
        parent::__construct($reflector->getDeclaringClass(), $attribute);
    }

}
