<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @method bool isPublic()
 * @method bool isPrivate()
 * @method bool isProtected()
 * @method bool isAbstract()
 * @method bool isFinal()
 * @method bool isConstructor()
 * @method bool isDestructor()
 * @method \Closure getClosure(?object $object = null)
 * @method int getModifiers()
 * @method mixed invoke(?object $object, mixed $args)
 * @method mixed invokeArgs(?object $object, array $args)
 * @method \ReflectionClass getDeclaringClass()
 * @method \ReflectionMethod getPrototype()
 * @method void setAccessible(bool $accessible)
 * @method bool inNamespace()
 * @method bool isClosure()
 * @method bool isDeprecated()
 * @method bool isInternal()
 * @method bool isUserDefined()
 * @method bool isGenerator()
 * @method bool isVariadic()
 * @method bool isStatic()
 * @method ?object getClosureThis()
 * @method ?\ReflectionClass getClosureScopeClass()
 * @method array getClosureUsedVariables()
 * @method string|false getDocComment()
 * @method int|false getEndLine()
 * @method ?\ReflectionExtension getExtension()
 * @method string|false getExtensionName()
 * @method string|false getFileName()
 * @method string getName()
 * @method string getNamespaceName()
 * @method int getNumberOfParameters()
 * @method int getNumberOfRequiredParameters()
 * @method array getParameters()
 * @method string getShortName()
 * @method int|false getStartLine()
 * @method array getStaticVariables()
 * @method bool returnsReference()
 * @method bool hasReturnType()
 * @method ?\ReflectionType getReturnType()
 * @method bool hasTentativeReturnType()
 * @method ?\ReflectionType getTentativeReturnType()
 * @method array getAttributes(?string $name = null, int $flags = 0)
 * @see \ReflectionMethod
 */
class Method extends BaseModel
{

    use HasName;

    public static function getReflectorClassName(): string
    {
        return \ReflectionMethod::class;
    }

}
