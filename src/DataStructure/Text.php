<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    InvalidArgumentException,
    JsonSerializable,
    Stringable;
use function get_debug_type,
             is_stringable,
             mb_strlen,
             mb_strpos,
             mb_substr,
             preg_valid;

/**
 * Transform a scalar to its stringable representation
 */
class Text implements Stringable, Countable, JsonSerializable
{

    protected string $text;
    protected int $length;
    protected array $offsets = [];

    public function __construct(mixed $text = '')
    {
        $this->_setText($text);
    }

    protected function _setText(mixed $text): static
    {
        if ( ! is_stringable($text) || is_null($text)) {
            throw new InvalidArgumentException(sprintf('Text of type %s is not stringable.', get_debug_type($text)));
        }

        if (is_scalar($text) && ! is_string($text)) {
            $text = json_encode($text, flags: JSON_THROW_ON_ERROR);
        }
        $this->text = (string) $text;
        $this->length = mb_strlen($this->text);

        return $this;
    }

    protected function buildOffsetMap(): void
    {

        if (empty($this->offsets)) {

            // UTF-8 offset map

            $this->offsets = [[], []];

            $offsets = &$this->offsets;

            for ($i = 0; $i < $this->length; $i ++ ) {
                $char = mb_substr($this->text, $i, 1);
                for ($j = 0; $j < strlen($char); $j ++ ) {
                    $offsets[0][] = $i;
                    $offsets[1][$i] ??= array_key_last($offsets[0]);
                }
            }
        }
    }

    protected function getUtfOffset(int $offset): ?int
    {

        if ($offset <= 0) {
            return $offset;
        }

        if ($this->isEmpty()) {
            return null;
        }
        $this->buildOffsetMap();
        return $this->offsets[0][$offset] ?? null;
    }

    protected function getNonUtfOffset(int $offset): ?int
    {
        if ($offset <= 0) {
            return $offset;
        }

        if ($this->isEmpty()) {
            return null;
        }
        $this->buildOffsetMap();
        return $this->offsets[1][$offset] ?? null;
    }

    public function indexOf(string|Stringable $needle, int $offset = 0): int
    {
        $needle = (string) $needle;

        if ($this->isEmpty()) {
            return $needle === '' && $offset === 0 ? 0 : -1;
        }

        if ($offset >= $this->length) {
            return -1;
        }

        if (preg_valid($needle)) {

            //translate char into byte offset

            if (is_null($offset = $this->getNonUtfOffset($offset))) {
                return -1;
            }

            if (preg_match($needle, $this->text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                // translate byte into char offset
                return $this->getUtfOffset($matches[0][1]) ?? -1;
            }
        } elseif (is_int($pos = mb_strpos($this->text, $needle, $offset))) {
            return $pos;
        }

        return -1;
    }

    public function lastIndexOf(string|Stringable $needle, int $offset = 0)
    {
        $result = -1;
        while (-1 !== $pos = $this->indexOf($needle, $offset)) {
            $result = $pos;
            $offset = $pos + 1;
        }

        return $result;
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

    public function __clone()
    {
        $this->offsets = [];
    }

    public function __toString(): string
    {
        return $this->text;
    }

    public function __serialize(): array
    {
        return [$this->text, $this->length, $this->offsets];
    }

    public function __unserialize(array $data): void
    {
        list($this->text, $this->length, $this->offsets) = $data;
    }

}
