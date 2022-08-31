<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use ArrayAccess,
    Countable;

/**
 * Python like Collection
 */
interface pCollection extends Countable, pIterable, ArrayAccess
{

    /**
     * Checks if collection has value
     */
    public function contains(mixed $value): bool;
}
