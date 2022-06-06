<?php

declare(strict_types=1);

namespace NGSOFT\Reflection\Attributes;

class ClassMethodAttribute extends ClassAttribute
{

    public readonly string $methodName;
    protected array $parameters = [];

    public function __construct(\ReflectionMethod $reflector, object $attribute)
    {

        $this->methodName = $reflector->getName();
        parent::__construct($reflector->getDeclaringClass(), $attribute);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

}
