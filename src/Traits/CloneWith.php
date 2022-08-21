<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

/**
 * Make Class Cloning simpler
 * @phan-file-suppress PhanTypeMismatchReturn
 */
trait CloneWith
{

    protected function cloneWith(array $properties = []): static
    {

        $clone = clone $this;

        foreach ($properties as $prop => $value) {

            $clone->{$prop} = $value;
        }

        return $clone;
    }

}
