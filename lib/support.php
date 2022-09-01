<?php

declare(strict_types=1);

/*
 * These functions are from illuminate/support, illuminate/collections
 * Modified to accepts types
 *
 * @link https://github.com/illuminate/support
 */

namespace
{
    if ( ! function_exists('trait_uses_recursive')) {

        /**
         * Returns all traits used by a trait and its traits.
         *
         * @param  string  $trait
         * @return array
         */
        function trait_uses_recursive(string $trait): array
        {
            $traits = class_uses($trait) ?: [];

            foreach ($traits as $trait)
            {
                $traits += trait_uses_recursive($trait);
            }

            return $traits;
        }

    }

    if ( ! function_exists('class_uses_recursive')) {

        /**
         * Returns all traits used by a class, its parent classes and trait of their traits.
         *
         * @param  object|string  $class
         * @return array
         */
        function class_uses_recursive(object|string $class): array
        {
            if (is_object($class)) {
                $class = get_class($class);
            }

            $results = [];

            foreach (array_reverse(class_parents($class)) + [$class => $class] as $class)
            {
                $results += trait_uses_recursive($class);
            }

            return array_unique($results);
        }

    }

    if ( ! function_exists('blank')) {

        /**
         * Determine if the given value is "blank".
         *
         * @param  mixed  $value
         * @return bool
         */
        function blank(mixed $value): bool
        {
            if (is_null($value)) {
                return true;
            }

            if (is_string($value)) {
                return trim($value) === '';
            }

            if (is_numeric($value) || is_bool($value)) {
                return false;
            }

            if ($value instanceof Countable) {
                return count($value) === 0;
            }

            return empty($value);
        }

    }


    if ( ! function_exists('filled')) {

        /**
         * Determine if a value is "filled".
         *
         * @param  mixed  $value
         * @return bool
         */
        function filled(mixed $value): bool
        {
            return ! blank($value);
        }

    }


    if ( ! function_exists('object_get')) {

        /**
         * Get an item from an object using "dot" notation.
         *
         * @param  object  $object
         * @param  string|null  $key
         * @param  mixed  $default
         * @return mixed
         */
        function object_get(object $object, string $key = null, mixed $default = null): mixed
        {
            if (is_null($key) || trim($key) === '') {
                return $object;
            }

            foreach (explode('.', $key) as $segment)
            {
                if ( ! is_object($object) || ! isset($object->{$segment})) {
                    return value($default);
                }

                $object = $object->{$segment};
            }

            return $object;
        }

    }



    if ( ! function_exists('transform')) {

        /**
         * Transform the given value if it is present.
         *
         * @param  mixed  $value
         * @param  callable  $callback
         * @param  mixed  $default
         * @return mixed|null
         */
        function transform(mixed $value, callable $callback, mixed $default = null): mixed
        {
            if (filled($value)) {
                return $callback($value);
            }

            if (is_callable($default)) {
                return $default($value);
            }

            return $default;
        }

    }


    if ( ! function_exists('value')) {

        /**
         * Return the default value of the given value.
         *
         * @param  mixed  $value
         * @return mixed
         */
        function value(mixed $value, ...$args): mixed
        {
            return $value instanceof Closure ? $value(...$args) : $value;
        }

    }



    if ( ! function_exists('class_basename')) {

        /**
         * Get the class "basename" of the given object / class.
         *
         * @param  string|object  $class
         * @return string
         */
        function class_basename(string|object $class): string
        {
            $class = is_object($class) ? get_class($class) : $class;

            return basename(str_replace('\\', '/', $class));
        }

    }
}


