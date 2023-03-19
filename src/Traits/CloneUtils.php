<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

/**
 * Use this trait for clone utilities
 *
 * 
 * @phan-file-suppress PhanTypeMismatchReturn
 */
trait CloneUtils
{

    /**
     * Get a clone of the current object
     */
    protected function clone(): static
    {
        return clone $this;
    }

    /**
     * Clone Object with defined property
     */
    protected function cloneWith(string $prop, mixed $value): static
    {
        $clone = $this->clone();

        if (property_exists($clone, $prop))
        {
            $clone->{$prop} = $value;
        }
        return $clone;
    }

    /**
     * Clone Object with defined properties
     */
    protected function cloneWithProperties(array $properties): static
    {

        $clone = $this->clone();

        foreach ($properties as $prop => $value)
        {
            if (property_exists($clone, $prop))
            {
                $clone->{$prop} = $value;
            }
        }

        return $clone;
    }

    /**
     * Clone object using named variadic properties
     *     Usage: $this->with(myfirstprop: 'value', mysecondprop: true ...)
     */
    protected function with(mixed ... $properties): static
    {
        return $this->cloneWithProperties($properties);
    }

    /**
     * To be used inside your __clone() method to clone a property that is an array
     */
    protected function cloneArray(array $array, bool $recursive = false)
    {

        // here the main array is already a copy

        foreach ($array as $offset => $value)
        {

            if (is_object($value))
            {
                $array[$offset] = clone $value;
            }
            if ($recursive && is_array($value))
            {
                $array[$offset] = $this->cloneArray($value, $recursive);
            }
            // the rest are scalar types
        }

        return $array;
    }

}
