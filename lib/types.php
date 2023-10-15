<?php

declare(strict_types=1);

use NGSOFT\DataStructure\SimpleIterator;
use NGSOFT\DataStructure\Sort;
use NGSOFT\Tools\TypeCheck;

/**
 * Checks if value is a list.
 */
function is_list(mixed $value): bool
{
    // mixed union, intersection type check
    if ( ! TypeCheck::checkType($value, 'ArrayAccess&Countable|iterable'))
    {
        return false;
    }

    if (is_array($value))
    {
        return array_is_list($value);
    }

    if (is_countable($value) && 0 === count($value))
    {
        return true;
    }

    if (is_array($value))
    {
        return $value === array_values($value);
    }

    // array|Traversable
    if (is_iterable($value))
    {
        $nextKey = -1;

        foreach ($value as $k => $_)
        {
            if ($k !== ++$nextKey)
            {
                return false;
            }
        }

        return true;
    }

    // ArrayAccess&Countable

    for ($offset = 0; $offset < count($value); ++$offset )
    {
        // isset can return false negative
        try
        {
            if (null === $value[$offset])
            {
                return false;
            }
        } catch (Throwable)
        {
            return false;
        }
    }

    return true;
}

/**
 * Alias of count.
 */
function len(mixed $countable): int
{
    if (is_countable($countable))
    {
        return count($countable);
    }

    throw new TypeError(sprintf('object of type %s has no len()', get_debug_type($countable)));
}

/**
 * Get a reversed iterable.
 */
function reversed(iterable $seq): iterable
{
    return SimpleIterator::of($seq)->entries(Sort::DESC);
}
