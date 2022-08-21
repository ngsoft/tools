<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use InvalidArgumentException,
    Stringable,
    Throwable;
use function is_stringable;

/**
 * Transfor a scalar to its stringable representation
 */
class Text implements Stringable
{

    protected string $text;

    public function __construct(
            mixed $text
    )
    {
        if ( ! is_stringable($text) || is_null($text)) {
            throw new InvalidArgumentException(sprintf('Text of type %s is not stringable.', get_debug_type($text)));
        }

        if (is_scalar($text) && ! is_string($text)) {
            $this->text = json_encode($text, flags: JSON_THROW_ON_ERROR);
            return;
        }

        $this->text = (string) $text;
    }

    public function __toString(): string
    {
        return (string) $this->text;
    }

}
