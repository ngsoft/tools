<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use NGSOFT\Tools\TypeCheck,
    Throwable;
use function array_is_list;

/**
 * Creates a new list
 */
function plist(iterable $list = []): pList
{
    return new pList($list);
}

/**
 *
 */
function is_list(mixed $value): bool
{

    if (
            ! TypeCheck::checkType(
                    $value,
                    'ArrayAccess&Countable|iterable'
            )
    ) {
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


    if (is_iterable($value)) {
        $nextKey = -1;

        foreach ($value as $k => $_) {
            if ($k !== ++ $nextKey) {
                return false;
            }
        }

        return true;
    }

    // ArrayAccess&Countable

    for ($offset = 0; $offset < count($value); $offset ++) {

        try {
            if ($value[$offset] === null) {
                return false;
            }
        } catch (Throwable) {
            return false;
        }
    }


    return true;
}
