<?php

declare(strict_types=1);

namespace NGSOFT\Reflection\Attributes;

use NGSOFT\Reflection\AttributeMetadata,
    ReflectionClass;

class ClassAttribute
{

    public readonly string $className;
    public object $attribute;

    public function __construct(ReflectionClass $reflector, object $attribute)
    {
        $this->className = $reflector->getName();
    }

    public function getAttributeMetadata(): AttributeMetadata
    {
        return new AttributeMetadata($this->attribute);
    }

    public function withAttribute(object $attribute): static
    {
        $clone = clone $this;
        $clone->attribute = $attribute;
        return $clone;
    }

}
