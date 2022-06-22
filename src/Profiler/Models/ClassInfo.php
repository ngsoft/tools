<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @method string getName()
 * @method bool isInternal()
 * @method bool isUserDefined()
 * @method bool isAnonymous()
 * @method bool isInstantiable()
 * @method bool isCloneable()
 * @method string|false getFileName()
 * @method int|false getStartLine()
 * @method int|false getEndLine()
 * @method string|false getDocComment()
 * @method ?\ReflectionMethod getConstructor()
 * @method bool hasMethod(string $name)
 * @method \ReflectionMethod getMethod(string $name)
 * @method array getMethods(?int $filter = null)
 * @method bool hasProperty(string $name)
 * @method \ReflectionProperty getProperty(string $name)
 * @method array getProperties(?int $filter = null)
 * @method bool hasConstant(string $name)
 * @method array getConstants(?int $filter = null)
 * @method array getReflectionConstants(?int $filter = null)
 * @method mixed getConstant(string $name)
 * @method \ReflectionClassConstant|false getReflectionConstant(string $name)
 * @method array getInterfaces()
 * @method array getInterfaceNames()
 * @method bool isInterface()
 * @method array getTraits()
 * @method array getTraitNames()
 * @method array getTraitAliases()
 * @method bool isTrait()
 * @method bool isEnum()
 * @method bool isAbstract()
 * @method bool isFinal()
 * @method int getModifiers()
 * @method bool isInstance(object $object)
 * @method object newInstance(mixed $args)
 * @method object newInstanceWithoutConstructor()
 * @method ?object newInstanceArgs(array $args = [])
 * @method \ReflectionClass|false getParentClass()
 * @method bool isSubclassOf(\ReflectionClass|string $class)
 * @method ?array getStaticProperties()
 * @method mixed getStaticPropertyValue(string $name, mixed $default)
 * @method void setStaticPropertyValue(string $name, mixed $value)
 * @method array getDefaultProperties()
 * @method bool isIterable()
 * @method bool isIterateable()
 * @method bool implementsInterface(\ReflectionClass|string $interface)
 * @method ?\ReflectionExtension getExtension()
 * @method string|false getExtensionName()
 * @method bool inNamespace()
 * @method string getNamespaceName()
 * @method string getShortName()
 * @method array getAttributes(?string $name = null, int $flags = 0)
 * @see \ReflectionClass
 */
class ClassInfo extends BaseModel
{

    public readonly string $name;

    public function __construct(
            object|string $reflector
    )
    {

        if ( ! $reflector instanceof \ReflectionClass) {
            $reflector = new \ReflectionClass($reflector);
        }
        parent::__construct($reflector);

        $this->name = $reflector->getName();
    }

}
