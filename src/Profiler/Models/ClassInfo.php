<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use ReflectionClass,
    ReflectionClassConstant,
    ReflectionMethod,
    ReflectionProperty;
use function NGSOFT\Tools\map;

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
 * @method ReflectionMethod getMethod(string $name)
 * @method array getMethods(?int $filter = null)
 * @method bool hasProperty(string $name)
 * @method ReflectionProperty getProperty(string $name)
 * @method array getProperties(?int $filter = null)
 * @method bool hasConstant(string $name)
 * @method array getConstants(?int $filter = null)
 * @method array getReflectionConstants(?int $filter = null)
 * @method mixed getConstant(string $name)
 * @method ReflectionClassConstant|false getReflectionConstant(string $name)
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
 * @method ReflectionClass|false getParentClass()
 * @method bool isSubclassOf(ReflectionClass|string $class)
 * @method ?array getStaticProperties()
 * @method mixed getStaticPropertyValue(string $name, mixed $default)
 * @method void setStaticPropertyValue(string $name, mixed $value)
 * @method array getDefaultProperties()
 * @method bool isIterable()
 * @method bool isIterateable()
 * @method bool implementsInterface(ReflectionClass|string $interface)
 * @method ?\ReflectionExtension getExtension()
 * @method string|false getExtensionName()
 * @method bool inNamespace()
 * @method string getNamespaceName()
 * @method string getShortName()
 * @method array getAttributes(?string $name = null, int $flags = 0)
 * @see ReflectionClass
 */
class ClassInfo extends BaseModel
{

    use HasName;

    public static function getReflectorClassName(): string
    {
        return ReflectionClass::class;
    }

    protected ?array $methods = null;
    protected ?array $properties = null;

    public function getClassMethod(string $name): ?Method
    {
        return $this->getClassMethods()[$name] ?? null;
    }

    /**
     * @return Method[]
     */
    public function getClassMethods(): array
    {

        if ( ! $this->methods) {
            $this->methods = map(function (\ReflectionMethod $method, &$key) {
                $key = $method->getName();
                return Method::create($method);
            }, $this->getMethods());
        }

        return $this->methods;
    }

    public function getClassProperty(string $name): ?Property
    {
        return $this->getClassProperties()[$name] ?? null;
    }

    /**
     * @return Property[]
     */
    public function getClassProperties(): array
    {

        if ( ! $this->properties) {
            $this->properties = map(function (\ReflectionProperty $prop, &$key) {
                $key = $prop->getName();
                return new Property($prop);
            }, $this->getProperties());
        }

        return $this->properties;
    }

}
