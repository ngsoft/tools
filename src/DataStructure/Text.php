<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use InvalidArgumentException,
    Stringable,
    Throwable;
use function is_stringable;

class Text implements Stringable
{

    protected string|Stringable $text;

    public function __construct(
            mixed $text
    )
    {
        if ( ! is_stringable($text)) {
            throw new InvalidArgumentException(sprintf('Text is not stringable.'));
        }

        if (is_scalar($text) && ! is_string($text)) {
            $this->text = json_encode($text, flags: JSON_THROW_ON_ERROR);
        } else { $this->text = $text; }
    }

    public function __toString(): string
    {
        return (string) $this->text;
    }

}
