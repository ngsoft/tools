<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

trait ClassUtils
{

    /**
     * Alias to static::class
     */
    final protected static function class(): string
    {
        return static::class;
    }

    /**
     * Get class name without the namespace
     */
    final protected static function classname(): string
    {
        return class_basename(static::class);
    }

    /**
     * Get the namespace of the class
     */
    final protected static function namespace(): string
    {
        return class_namespace(static::class);
    }

    /**
     * Checks if class extends or is static
     */
    final protected static function isSelf(object|string $class)
    {
        return is_a($class, static::class, is_string($class));
    }

}
