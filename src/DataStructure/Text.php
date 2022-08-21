<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    InvalidArgumentException,
    JsonSerializable,
    NGSOFT\Traits\CloneWith,
    Stringable;
use function get_debug_type,
             is_stringable,
             mb_strlen;

/**
 * Transform a scalar to its stringable representation
 */
class Text implements Stringable, Countable, JsonSerializable
{

    use CloneWith;

    protected string $text;
    protected int $length;

    public function __construct(mixed $text)
    {
        if ( ! is_stringable($text) || is_null($text)) {
            throw new InvalidArgumentException(sprintf('Text of type %s is not stringable.', get_debug_type($text)));
        }

        if (is_scalar($text) && ! is_string($text)) {
            $this->text = json_encode($text, flags: JSON_THROW_ON_ERROR);
            return;
        }

        $this->text = (string) $text;
        $this->length = mb_strlen($this->text);
    }

    public function count(): int
    {
        return $this->length;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function jsonSerialize(): mixed
    {
        return $this->text;
    }

    public function toString(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return $this->text;
    }

    public function __serialize(): array
    {
        return [$this->text, $this->length];
    }

    public function __unserialize(array $data): void
    {
        @list($this->text, $this->length) = $data;
    }

}
