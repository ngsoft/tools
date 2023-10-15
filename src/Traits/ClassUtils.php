<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

trait ClassUtils
{
    /**
     * Alias to static::class.
     */
    protected static function class(object|string|null $class = null): string
    {
        if (is_object($class))
        {
            return get_class($class);
        }

        if (is_string($class))
        {
            return $class;
        }

        return static::class;
    }

    /**
     * Get class name without the namespace.
     */
    protected static function classname(object|string|null $class = null): string
    {
        return basename(str_replace(NAMESPACE_SEPARATOR, '/', static::class($class)));
    }

    /**
     * Get the namespace of the class.
     */
    protected static function namespace(object|string|null $class = null): string
    {
        $class = static::class($class);

        if ( ! str_contains($class, NAMESPACE_SEPARATOR))
        {
            return '';
        }
        return substr($class, 0, strrpos($class, NAMESPACE_SEPARATOR));
    }

    /**
     * Checks if class extends or is static.
     */
    protected static function isSelf(object|string $class): bool
    {
        return is_a($class, static::class, is_string($class));
    }
}
