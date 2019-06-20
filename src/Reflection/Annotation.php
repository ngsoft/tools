<?php

namespace NGSOFT\Tools\Reflection;

use ArrayObject;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;

/**
 * @property string $className
 * @property string|null $attributeName
 * @property ReflectionObject|ReflectionClass|ReflectionProperty|ReflectionMethod|ReflectionClassConstant|ReflectionFunction $reflector
 * @property string $tag
 * @property mixed $value
 * @property string $annotationType
 * @property string|null $description
 */
class Annotation extends ArrayObject {

    const ANNOTATION_TYPES = [
        ReflectionObject::class => "OBJECT",
        ReflectionClass::class => "CLASS",
        ReflectionProperty::class => "PROPERTY",
        ReflectionMethod::class => "METHOD",
        ReflectionClassConstant::class => "CONSTANT",
        ReflectionFunction::class => "FUNCTION",
    ];

    /**
     *
     * @param ReflectionClass $classRefl
     * @param ReflectionObject|ReflectionClass|ReflectionProperty|ReflectionMethod|ReflectionClassConstant|ReflectionFunction $reflector
     * @param string $tag
     * @param mixed $value
     * @param ?string $description
     */
    public function __construct(\ReflectionClass $classRefl, $reflector, string $tag, $value, string $description = null) {
        parent::__construct(
                [
                    "annotationType" => null,
                    "className" => null,
                    "attributeName" => null,
                    "reflector" => null,
                    "tag" => null,
                    "value" => null,
                    "description" => null
                ],
                ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS
        );

        $this->tag = $tag;
        $this->value = $value;
        if (is_string($description) and mb_strlen($description) > 0) $this->description = $description;

        foreach (static::ANNOTATION_TYPES as $className => $type) {
            if ($reflector instanceof $className) {
                $this->annotationType = $type;
                $this->reflector = $reflector;
                $this->className = $classRefl->name;
                if (isset($reflector->class)) $this->attributeName = $reflector->name;

                break;
            }
        }
    }

}
