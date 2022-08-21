<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

/**
 * Make Class Cloning simpler
 * @phan-file-suppress PhanTypeMismatchReturn
 */
trait CloneWith
{

    protected function getClone(): static
    {
        return clone $this;
    }

    protected function cloneWith(array $properties = []): static
    {

        $clone = $this->getClone();

        $propertyList = array_keys(get_object_vars($this));

        foreach ($properties as $prop => $value) {

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
