<?php

declare(strict_types=1);

namespace NGSOFT\Types;

class ValueError extends \Error
{

    public static function for(mixed $value, object|string $class = pCollection::class): static
    {

        if (is_object($value)) {
            $value = sprintf('object(%s)#%d', get_class($value), spl_object_id($value));
        } elseif (is_array($value)) {
            try {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $value = '[]';
            }
        } elseif (is_stringable($value)) {
            $value = str_val($value);
        }

        return new static(sprintf('%s is not in %s.', $value, class_basename($class)));
    }

}
