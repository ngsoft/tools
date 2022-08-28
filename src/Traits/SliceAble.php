<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use NGSOFT\DataStructure\Slice;

trait SliceAble
{

    protected function getSlice(string|int $offset): Slice
    {
        return Slice::of($offset);
    }

    protected function sliceValue(Slice $slice, mixed $value): array
    {
        return $slice->slice($value);
    }

}
