<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @method string getName()
 * @method bool isPassedByReference()
 * @method bool canBePassedByValue()
 * @method \ReflectionFunctionAbstract getDeclaringFunction()
 * @method ?\ReflectionClass getDeclaringClass()
 * @method ?\ReflectionClass getClass()
 * @method bool hasType()
 * @method ?\ReflectionType getType()
 * @method bool isArray()
 * @method bool isCallable()
 * @method bool allowsNull()
 * @method int getPosition()
 * @method bool isOptional()
 * @method bool isDefaultValueAvailable()
 * @method mixed getDefaultValue()
 * @method bool isDefaultValueConstant()
 * @method ?string getDefaultValueConstantName()
 * @method bool isVariadic()
 * @method bool isPromoted()
 * @method array getAttributes(?string $name = null, int $flags = 0)
 * @see \ReflectionParameter
 */
class Parameter extends TypeParser
{

    use HasName;

    public static function getReflectorClassName(): string
    {
        return \ReflectionParameter::class;
    }

}
