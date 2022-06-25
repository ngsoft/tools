<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use BadMethodCallException,
    Closure,
    ReflectionClass,
    ReflectionMethod;

trait MacroAble
{

    protected static array $macros = [];

    /**
     * register a custom macro.
     */
    public static function macro(string $name, object|callable $macro): void
    {
        self::$macros[$name] = $macro;
    }

    /**
     * Mix another object into the class.
     */
    public static function mixin(object $mixin, bool $replace = true): void
    {

        /** @var ReflectionMethod $method */
        foreach (
                (new ReflectionClass($mixin))->getMethods(
                        ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
                ) as $method
        ) {

            if ($replace || ! isset(static::$macros[$method->getName()])) {
                $method->setAccessible(true);
                static::$macros[$method->getName()] = $method->invoke($mixin);
            }
        }
    }

    /**
     * Checks if macro is registered.
     */
    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Flush the existing macros.
     *
     * @return void
     */
    public static function flushMacros(): void
    {
        static::$macros = [];
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ( ! static::hasMacro($name)) {
            throw new BadMethodCallException(sprintf(
                                    'Method %s::%s does not exist.', static::class, $name
            ));
        }

        $macro = static::$macros[$name];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        return $macro(...$arguments);
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        if ( ! static::hasMacro($name)) {
            throw new BadMethodCallException(sprintf(
                                    'Method %s::%s does not exist.', static::class, $name
            ));
        }

        $macro = static::$macros[$name];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo(null, static::class);
        }

        return $macro(...$arguments);
    }

}
