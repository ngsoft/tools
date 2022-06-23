<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @method string getName()
 * @method mixed getValue(?object $object = null)
 * @method void setValue(mixed $objectOrValue, mixed $value)
 * @method bool isInitialized(?object $object = null)
 * @method bool isPublic()
 * @method bool isPrivate()
 * @method bool isProtected()
 * @method bool isStatic()
 * @method bool isReadOnly()
 * @method bool isDefault()
 * @method bool isPromoted()
 * @method int getModifiers()
 * @method \ReflectionClass getDeclaringClass()
 * @method string|false getDocComment()
 * @method void setAccessible(bool $accessible)
 * @method ?\ReflectionType getType()
 * @method bool hasType()
 * @method bool hasDefaultValue()
 * @method mixed getDefaultValue()
 * @method array getAttributes(?string $name = null, int $flags = 0)
 * @see \ReflectionProperty
 */
class Property extends BaseModel
{

    use HasName;

    public static function getReflectorClassName(): string
    {
        return \ReflectionProperty::class;
    }

}
