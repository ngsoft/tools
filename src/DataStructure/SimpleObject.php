<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use OutOfBoundsException;
use function get_debug_type;

class SimpleObject extends ArrayAccessCommon
{

    public static function create(array $array = [], bool $recursive = false): static
    {
        return new static($array, $recursive);
    }

    protected function append(mixed $offset, mixed $value): void
    {

        if (null === $offset) {
            $this->storage[] = $value;
            return;
        }

        if (!is_int($offset) && !is_int($offset)) {
            throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
        }

        $this->offsetUnset($offset);
        if ($value instanceof self) $value = $value->storage;
        $this->storage[$offset] = $value;
    }

}
