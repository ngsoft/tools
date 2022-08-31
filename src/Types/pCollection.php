<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use ArrayAccess,
    Countable;

/**
 * Python like Collection
 */
abstract class pCollection implements Countable, pIterable, ArrayAccess
{

    /**
     * Checks if collection has value
     */
    public function contains(mixed $value): bool
    {

        if (is_null($value)) {
            return false;
        }


        return $this->count($value) > 0;
    }

    /**
     * Count number of occurences of value
     * if value is null returns the collections size
     */
    abstract public function count(mixed $value = null): int;
}
