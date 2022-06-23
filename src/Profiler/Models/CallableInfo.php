<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @method bool isDisabled()
 * @method mixed invoke(mixed $args)
 * @method mixed invokeArgs(array $args)
 * @method \Closure getClosure()
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
 * @see \ReflectionFunction
 */
class CallableInfo extends BaseModel
{

    use HasName;

    public static function getReflectorClassName(): string
    {
        return \ReflectionFunction::class;
    }

}
