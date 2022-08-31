<?php

declare(strict_types=1);

namespace NGSOFT\Types;

class IndexError extends \OutOfRangeException
{

    public static function for(mixed $index, object|string $class = pCollection::class): static
    {

        if (is_object($index)) {
            $index = sprintf('object(%s)#%d', get_class($index), spl_object_id($index));
        } elseif (is_array($index)) {
            try {
                $index = json_encode($index, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $index = '[]';
            }
        } elseif (is_stringable($index)) {
            $index = str_val($index);
        }

        return new static(sprintf('%s index %s out of range.', class_basename($class), $index));
    }

}
