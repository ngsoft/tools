<?php

declare(strict_types=1);

namespace NGSOFT\Profiler;

class Profiler
{

    public function getProfile(string|object $class)
    {

    }

    public function getClassInfo(\ReflectionClass $class): Models\ClassInfo
    {
        return Models\ClassInfo::create($class);
    }

    public function getFunction(\ReflectionFunction $callable)
    {

    }

    public function getMethod(\ReflectionMethod $method)
    {

    }

    public function getDefaultValue(\ReflectionParameter|\ReflectionProperty $reflector): mixed
    {

        $hasDefault = $reflector instanceof \ReflectionProperty ? $reflector->hasDefaultValue() : $reflector->isDefaultValueAvailable();
        $nullable = $reflector->getType() === null || str_contains((string) $reflector->getType(), 'null');

        if ($reflector instanceof \ReflectionParameter && $reflector->isVariadic()) {
            return [];
        }

        if ($hasDefault) {
            return $reflector->getDefaultValue();
        }

        if ($nullable) {
            return null;
        }

        throw new \ReflectionException(
                        sprintf('Cannot get default value for %s $%s type %s',
                                $reflector instanceof \ReflectionProperty ? 'property' : 'parameter',
                                $reflector->getName(), (string) $reflector->getType())
        );
    }

    public function getParameter(\ReflectionParameter $parameter)
    {
        return Models\ParameterInfo::create($parameter);
    }

    public function getProperty(\ReflectionProperty $property)
    {

    }

    public function getCallable(callable $callable): \ReflectionMethod|\ReflectionFunction
    {

        if ($callable instanceof \Closure) {
            return new \ReflectionFunction($callable);
        }

        if (is_array($callable)) {
            list($class, $method) = $callable;
            if ( ! method_exists($class, $method)) {
                throw NotCallable::fromInvalidCallable($callable);
            }

            return new \ReflectionMethod($class, $method);
        }


        if (is_object($callable) && method_exists($callable, '__invoke')) {
            return new \ReflectionMethod($callable, '__invoke');
        }

        throw new NotCallable(sprintf('%s is not a callable', is_string($callable) ? $callable : get_debug_type($callable) ));
    }

}
