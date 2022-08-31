<?php

declare(strict_types=1);

namespace NGSOFT\Types\Traits;

use NGSOFT\Types\{
    IndexError, Slice
};

trait IsSliceable
{

    /**
     * Translate negative offset as real offset,
     * Slice offset as list of offsets
     */
    protected function getOffset(Slice|int|string|null $offset): Slice|int
    {

        if (is_null($offset)) {
            return $this->count();
        }
        if (is_string($offset) && ! Slice::isValid($offset)) {
            throw IndexError::for($offset, $this);
        }


        if (is_int($offset) && $offset < 0) {
            $offset += $this->count();

            if ($offset === -1 && ! $this->count()) {
                $offset = 0;
            }
        } elseif (is_string($offset)) {
            $offset = Slice::of($offset);
        }

        return $offset;
    }

    abstract public function count(mixed $value = null): int;
}
