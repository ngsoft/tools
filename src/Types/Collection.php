<?php

declare(strict_types=1);

namespace NGSOFT\Types;

/**
 * Python like Collection
 */
interface pCollection extends \Countable, \Traversable, \ArrayAccess
{

    /**
     * Checks if collection has value
     */
    public function contains(mixed $value): bool;
}
