<?php

declare(strict_types=1);

namespace NGSOFT\Profiler;

use Closure;
use NGSOFT\Profiler\Models\{
    CallableInfo, ClassInfo, Method, Parameter, Property
};
use ReflectionClass,
    ReflectionFunction,
    ReflectionMethod,
    ReflectionParameter,
    ReflectionProperty;
use function get_debug_type;

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

    protected function getCallableReflector(callable $callable): ReflectionMethod|ReflectionFunction
    {

        if ($callable instanceof Closure) {
            return new ReflectionFunction($callable);
        }

        if (is_array($callable)) {
            list($class, $method) = $callable;
            if ( ! method_exists($class, $method)) {
                throw NotCallable::fromInvalidCallable($callable);
            }

            return new ReflectionMethod($class, $method);
        }


        if (is_object($callable) && method_exists($callable, '__invoke')) {
            return new ReflectionMethod($callable, '__invoke');
        }

        throw new NotCallable(sprintf('%s is not a callable', is_string($callable) ? $callable : get_debug_type($callable) ));
    }

}
