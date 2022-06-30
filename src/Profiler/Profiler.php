<?php

declare(strict_types=1);

namespace NGSOFT\Profiler;

use Closure,
    ErrorException;
use NGSOFT\{
    Profiler\Models\CallableInfo, Profiler\Models\ClassInfo, Profiler\Models\Method, Profiler\Models\Parameter, Profiler\Models\Property, Tools
};
use ReflectionClass,
    ReflectionException,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty;
use function get_debug_type,
             str_contains;

/**
 * Profiler Factory
 */
class Profiler
{

    public function getProfile(string|object $class): ?ClassInfo
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        if (class_exists($class) || interface_exists($class) || trait_exists($class)) {
            return $this->getClassInfo(new \ReflectionClass($class));
        }
        return null;
    }

    public function getClassInfo(ReflectionClass $class): ClassInfo
    {
        return ClassInfo::create($class);
    }

    public function getFunction(ReflectionFunction $callable): CallableInfo
    {
        return CallableInfo::create($callable);
    }

    public function getMethod(ReflectionMethod $method): Method
    {
        return Method::create($method);
    }

    public function getParameter(ReflectionParameter $parameter): Parameter
    {
        return Parameter::create($parameter);
    }

    public function getProperty(ReflectionProperty $property): Property
    {
        return Property::create($property);
    }

    public function getCallable(callable $callable): CallableInfo|Method
    {

        $reflector = $this->getCallableReflector($callable);

        return $reflector instanceof ReflectionMethod ? $this->getMethod($reflector) : $this->getFunction($reflector);
    }

    public function getCallableReflector(callable|array|string $callable): ReflectionMethod|ReflectionFunction
    {

        $orig = $callable;

        if ($callable instanceof Closure) {
            return new ReflectionFunction($callable);
        }


        if (is_string($callable)) {
            // MyClass::myMethod(), static function can be callable
            // but can't use ReflectionFuntion
            if (str_contains($callable, '::')) {
                $callable = explode('::', $callable);
            } elseif (is_callable($callable)) {
                return new ReflectionFunction($callable);
            }
        }


        if (is_array($callable) && count($callable) === 2) {
            list($class, $method) = $callable;

            if (
                    (is_string($class) || is_object($class)) &&
                    is_string($method) &&
                    method_exists($class, $method)
            ) {
                return new ReflectionMethod($class, $method);
            }


            throw NotCallable::fromInvalidCallable($callable);
        }


        if (is_object($callable) && method_exists($callable, '__invoke')) {
            return new ReflectionMethod($callable, '__invoke');
        }


        throw new NotCallable(sprintf('%s is not a callable', is_string($orig) ? $orig : get_debug_type($orig) ));
    }

}
