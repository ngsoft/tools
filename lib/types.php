<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use NGSOFT\{
    Tools\TypeCheck, Types\Iterators\pIterator
};
use Throwable,
    TypeError;
use function array_is_list,
             get_debug_type;

/**
 * Creates a new list
 */
function plist(iterable $list = []): pList
{
    return new pList($list);
}

/**
 * Checks if value is a list
 */
function is_list(mixed $value): bool
{

    // mixed union, intersection type check
    if ( ! TypeCheck::checkType($value, 'ArrayAccess&Countable|iterable')) {
        return false;
    }


    if (is_array($value)) {
        return array_is_list($value);
    }


    if (is_countable($value) && count($value) === 0) {
        return true;
    }


    if (is_array($value)) {
        return $value === array_values($value);
    }


    // array|Traversable
    if (is_iterable($value)) {
        $nextKey = -1;

        foreach ($value as $k => $_)
        {
            if ($k !== ++ $nextKey) {
                return false;
            }
        }

        return true;
    }

    // ArrayAccess&Countable

    for ($offset = 0; $offset < count($value); $offset ++ )
    {

        // isset can return false negative
        try
        {
            if ($value[$offset] === null) {
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
 * Alias of count
 */
function len(mixed $countable): int
{

    if (is_countable($countable)) {
        return count($countable);
    }

    throw new TypeError(sprintf('object of type %s has no len()', get_debug_type($countable)));
}

/**
 * Get a reversed iterable
 */
function reversed(iterable $seq): iterable
{

    return pIterator::of($seq)->entries(Sort::DESC);
}
