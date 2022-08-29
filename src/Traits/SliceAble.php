<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use NGSOFT\DataStructure\Slice;

trait SliceAble
{

    /**
     * Checks if input is a slice
     */
    protected function isSlice(string $input): bool
    {
        return Slice::isValid($input);
    }

    /**
     * Create a Slice Instance
     */
    protected function getSlice(string $offset): Slice
    {
        return Slice::of($offset);
    }

    /**
     * Returns a slice of an array like
     */
    protected function sliceValue(Slice $slice, mixed $value): array
    {
        return $slice->slice($value);
    }

    /**
     * Returns a string of a slice
     */
    protected function joinSliceValue(Slice $slice, mixed $value, mixed $glue = ''): string
    {
        return $slice->join($glue, $value);
    }

}
