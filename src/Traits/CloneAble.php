<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use NGSOFT\Tools;

/**
 * Make Class Cloning easier, does not implements: __clone():void
 *
 * @phan-file-suppress PhanTypeMismatchReturn
 */
trait CloneAble
{

    /**
     * Get a copy of the current object
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Helper for __clone that can clone objects in array properties recursively
     */
    protected function cloneArray(array $array, bool $recursive = true): array
    {
        return Tools::cloneArray($array, $recursive);
    }

    /**
     * Overrides properties in the clone
     * properties is variadic so you can use:
     *      return $this->with(myPropertyName: 'value', myOtherProperty: false);
     */
    protected function with(mixed ...$properties): static
    {

        $clone = $this->copy();

        if ( ! count($properties)) {
            return $clone;
        }

        $propertyList = array_keys(get_object_vars($this));

        foreach ($properties as $prop => $value) {
            // in order of assignement (class header, constructor)
            if (is_int($prop)) {
                $prop = $propertyList[$prop] ?? null;
            }


            if ( ! $prop || ! property_exists($clone, $prop)) {
                continue;
            }

            $clone->{$prop} = $value;
        }

        return $clone;
    }

}
