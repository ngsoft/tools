<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
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
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class Text implements Stringable, Countable, ArrayAccess, JsonSerializable
{

    protected string $text;
    protected int $length;
    protected array $offsets = [];

    public static function of(mixed $text): static
    {
        return new static($text);
    }

    public function __construct(mixed $text = '')
    {
        $this->setText($text);
    }

    protected function convert(mixed $text): string
    {
        if ( ! is_stringable($text)) {
            throw new InvalidArgumentException(sprintf('Text of type %s is not stringable.', get_debug_type($text)));
        }

        if (is_null($text)) {
            return '';
        }

        if (is_scalar($text) && ! is_string($text)) {
            $text = json_encode($text, flags: JSON_THROW_ON_ERROR);
        }
        return (string) $text;
    }

    protected function setText(mixed $text): static
    {
        $this->text = $this->convert($text);
        $this->length = mb_strlen($this->text);

        return $this;
    }

    public function __clone()
    {
        $this->offsets = [];
    }

    protected function withText(mixed $text): static
    {
        $clone = clone $this;
        return $clone->setText($text);
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

    protected function translateOffset(int $offset): int
    {
        return $offset < 0 ? $this->length + $offset : $offset;
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

    /**
     * The at() method takes an integer value and returns the character located at the specified offset
     */
    public function at(int $offset = 0): ?string
    {


        $offset = $this->translateOffset($offset);

        if ($offset >= $this->length || $offset < 0) {
            return null;
        }

        return mb_substr($this->text, $offset, 1);
    }

    /**
     * The concat() method concatenates the string arguments to the current Text
     */
    public function concat(mixed ...$strings): static
    {
        $str = $this->text;
        foreach ($strings as $string) {
            $str .= $this->convert($string);
        }
        return $this->withText($str);
    }

    /**
     * Converts Text to lower case
     */
    public function toLowerCase(): static
    {
        return $this->withText(mb_strtolower($this->text));
    }

    /**
     * Converts Text to upper case
     */
    public function toUpperCase(): static
    {
        return $this->withText(mb_strtoupper($this->text));
    }

    /**
     * The endsWith() method determines whether a string ends with the characters of a specified string, returning true or false as appropriate.
     */
    public function endsWith(mixed $needle, bool $ignoreCase = false): bool
    {

        $needle = $this->convert($needle);
        $haystack = $this->text;

        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }

        return str_ends_with($haystack, $needle);
    }

    /**
     * The startsWith() method determines whether a string begins with the characters of a specified string, returning true or false as appropriate.
     */
    public function startsWith(mixed $needle, bool $ignoreCase = false): bool
    {
        $needle = $this->convert($needle);
        $haystack = $this->text;

        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }
        return str_starts_with($haystack, $needle);
    }

    /**
     * The includes() method performs a search to determine whether one string may be found within another string, returning true or false as appropriate.
     */
    public function contains(mixed $needle, bool $ignoreCase = false)
    {

        $needle = $this->convert($needle);
        $haystack = $this->text;

        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }


        return str_contains($haystack, $needle);
    }

    /**
     * The includes() method performs a case-sensitive search to determine whether one string may be found within another string, returning true or false as appropriate.
     */
    public function includes(mixed $needle): bool
    {
        return $this->contains($needle);
    }

    /**
     * The match() method retrieves the result of matching a string against a regular expression.
     * @return string[]
     */
    public function match(string $pattern): array
    {
        return preg_exec($pattern, $this->text);
    }

    /**
     * The matchAll() method returns an iterator of all results matching a string against a regular expression, including capturing groups.
     */
    public function matchAll(string $pattern): array
    {
        return preg_exec($pattern, $this->text, 0);
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    public function offsetExists(mixed $offset): bool
    {
        return $this->at($offset) !== null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->at($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // nothing to do
    }

    public function offsetUnset(mixed $offset): void
    {
        // nothing to do
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
        return [$this->text, $this->length, $this->offsets];
    }

    public function __unserialize(array $data): void
    {
        list($this->text, $this->length, $this->offsets) = $data;
    }

}
