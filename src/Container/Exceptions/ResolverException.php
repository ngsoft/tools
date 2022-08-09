<?php

declare(strict_types=1);

namespace NGSOFT\Container\Exceptions;

use NGSOFT\Container\ContainerResolver;

class ResolverException extends ContainerError
{

    public static function notTwice(object $resolver)
    {
        return new static(
                sprintf('Cannot add the same resolver [%s#%d] instance twice.', get_class($resolver), spl_object_id($resolver))
        );
    }

    public static function invalidCallable(mixed $callable): static
    {
        if (is_object($callable)) {
            $message = sprintf('Instance of %s is not a callable', get_class($callable));
        } elseif (is_array($callable) && isset($callable[0], $callable[1])) {
            $class = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
            $extra = method_exists($class, '__call') || method_exists($class, '__callStatic') ? ' A __call() or __callStatic() method exists but magic methods are not supported.' : '';
            $message = sprintf('%s::%s() is not a callable.%s', $class, $callable[1], $extra);
        } else {
            $message = var_export($callable, true) . ' is not a callable';
        }

        return new static($message);
    }

}
