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
             mb_strtolower,
             mb_strtoupper,
             mb_substr;
use function NGSOFT\Tools\{
    every, map, some
};
use function preg_exec,
             preg_valid,
             str_contains,
             str_ends_with,
             str_starts_with;

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

        if ($text instanceof self) {
            return $text;
        }
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

            for ($i = 0; $i < $this->length; $i ++) {
                $char = mb_substr($this->text, $i, 1);
                for ($j = 0; $j < strlen($char); $j ++) {
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

    protected function getChars(array $chars): array
    {
        $result = [];

        foreach ($chars as $char) {
            $result[0] ??= '';
            $result[0] .= $this->convert($char);
        }
        return $result;
    }

    protected function getSlice(string $range): ?Range
    {


        if (preg_test('#^:{1,2}$#', $range)) {
            $result = Range::create(0, $this->length);
        } elseif (is_numeric($range) && $this->isValidOffset($range)) {
            $start = $this->translateOffset((int) $range);
            $result = Range::create($start, $start + 1);
        } elseif ($matches = preg_exec('#^(-?\d+)?(?:\:(-?\d+)?)?(?:\:(-?\d+)?)?$#', $range)) {
            @list(, $start, $stop, $step) = $matches;

            if ((string) $step === '') {
                $step = 1;
            }

            $step = intval($step);

            if ((string) $start === '') {
                $start = $step > 0 ? 0 : -1;
            }

            $start = intval($start);
            if ((string) $stop === '') {
                $stop = $step > 0 ? $length : -$length - 1;
            }

            $stop = intval($stop);

            // python does not change signs for slices
            $positive = $start >= 0;

            $result = static::create($start, $stop, $step);
        }

        if ( ! isset($result) || $this->isValidRange($result)) {
            return null;
        }

        return $result;
    }

    protected function isValidOffset(mixed $offset): bool
    {

        if ( ! is_numeric($offset)) {
            return false;
        }

        return in_range($this->translateOffset((int) $offset), 0, $this->length - 1);
    }

    protected function isValidRange(Range $range): bool
    {
        [$start, $stop, $step] = [$this->translateOffset($range->start), $this->translateOffset($range->stop), $range->step];
        return $step > 0 ? $stop > $start : $stop < $start;
    }

    /**
     * The indexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the first occurrence of the specified substring
     */
    public function indexOf(mixed $needle, int $offset = 0): int
    {
        $needle = $this->convert($needle);

        if (empty($needle) || $this->isEmpty()) {
            return -1;
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

    /**
     * Alias of indexOf
     */
    public function search(mixed $needle)
    {
        return $this->indexOf($this->convert($needle));
    }

    /**
     * The lastIndexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the last occurrence of the specified substring.
     */
    public function lastIndexOf(mixed $needle, int $offset = 0)
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
        if (is_iterable($needle)) {
            return some(fn($n) => $this->endsWith($n, $ignoreCase), $needle);
        }
        $needle = $this->convert($needle);
        $haystack = $this->text;

        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }
        if (empty($needle)) {
            return false;
        }
        return str_ends_with($haystack, $needle);
    }

    /**
     * The startsWith() method determines whether a string begins with the characters of a specified string, returning true or false as appropriate.
     */
    public function startsWith(mixed $needle, bool $ignoreCase = false): bool
    {

        if (is_iterable($needle)) {
            return some(fn($n) => $this->startsWith($n, $ignoreCase), $needle);
        }


        $needle = $this->convert($needle);
        $haystack = $this->text;

        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }

        if (empty($needle)) {
            return false;
        }
        return str_starts_with($haystack, $needle);
    }

    /**
     * The includes() method performs a search to determine whether one string may be found within another string, returning true or false as appropriate.
     */
    public function contains(mixed $needle, bool $ignoreCase = false): bool
    {

        if (is_iterable($needle)) {
            return every(fn($n) => $this->contains($n, $ignoreCase), $needle);
        }

        $needle = $this->convert($needle);
        $haystack = $this->text;

        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }


        if (empty($needle)) {
            return false;
        }

        return str_contains($haystack, $needle);
    }

    /**
     * Determine if a given string contains all needles
     */
    public function containsAll(iterable $needles, bool $ignoreCase = false): bool
    {
        return $this->contains($needles, $ignoreCase);
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

    /**
     * Pad the left side of a string with another.
     */
    public function padStart(int $length = 1, mixed $pad = ' '): static
    {

        $length = max(0, $length);
        $pad = $this->convert($pad);

        if (empty($pad) || empty($length)) {
            return $this;
        }

        $str = $this->text;
        $total = $this->length + $length;

        while (mb_strlen($str) < $total) {
            $str = $pad . $str;
        }

        return $this->withText(mb_substr($str, $total - mb_strlen($str)));
    }

    /**
     * Pad the right side of a string with another.
     */
    public function padEnd(int $length = 1, mixed $pad = ' '): static
    {

        $length = max(1, $length);
        $pad = $this->convert($pad);

        if (empty($pad) || empty($length)) {
            return $this;
        }

        $str = $this->text;
        $total = $this->length + $length;

        while (mb_strlen($str) < $total) {
            $str .= $pad;
        }

        return $this->withText(mb_substr($str, 0, $total));
    }

    /**
     * Pad on both sides of a string with another.
     */
    public function pad(int $length = 2, mixed $pad = ' '): static
    {
        $length = max(0, $length);

        $pad = $this->convert($pad);

        if (empty($pad) || empty($length)) {
            return $this;
        }

        $right = intval(ceil($length / 2));
        $left = $length - $right;

        return $this->padEnd($right, $pad)->padStart($left, $pad);
    }

    /**
     * The repeat() method constructs and returns a new string which contains the specified number of copies of the string on which it was called, concatenated together.
     */
    public function repeat(int $times): static
    {

        $times = max(0, $times);
        $str = '';

        for ($i = 0; $i < $times; $i ++ ) {
            $str .= $this->text;
        }
        return $this->withText($str);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     */
    public function replace(mixed $search, mixed $replacement): static
    {

        if (is_iterable($search)) {
            $result = $this;
            $index = 0;
            foreach ($search as $pattern) {
                if (is_iterable($replacement)) {
                    $replace = $replacement[$index] ?? '';
                } else { $replace = $replacement; }
                $result = $result->replace($pattern, $replace);
                $index ++;
            }
            return $result;
        }

        $search = $this->convert($search);

        if (empty($search)) {
            return $this;
        }

        $len = mb_strlen($search);

        $replacement = $this->convert($replacement);

        $index = $this->indexOf($search);

        if ($index === -1) {
            return $this;
        }

        $str = mb_substr($this->text, 0, $index);
        $str .= $replacement;
        $str .= mb_substr($this->text, $index + $len);
        return $this->withText($str);
    }

    public function replaceAll(mixed $search, mixed $replacement): static
    {

        if (is_iterable($search)) {
            $result = $this;
            $index = 0;
            foreach ($search as $pattern) {
                if (is_iterable($replacement)) {
                    $replace = $replacement[$index] ?? '';
                } else { $replace = $replacement; }
                $result = $result->replaceAll($pattern, $replace);
                $index ++;
            }
            return $result;
        }

        $search = $this->convert($search);

        if (empty($search)) {
            return $this;
        }

        $replacement = $this->convert($replacement);
        $method = preg_valid($search) ? 'preg_replace' : 'str_replace';

        return $this->withText($method($search, $replacement, $this->text));
    }

    /**
     * Reverse the string
     */
    public function reverse(): static
    {
        return $this->withText(implode('', array_reverse(mb_str_split($this->text))));
    }

    /**
     * The slice() method extracts a section of a string and returns it as a new string
     */
    public function slice(int $start = 0, ?int $end = null): static
    {

        $end ??= $this->length;

        if ( ! in_range($start, -$this->length, $this->length - 1)) {
            return $this->withText('');
        }


        if ($start < 0) {
            $start += $this->length;
        }

        if ($end < 0) {
            $end += $this->length;
        }


        $str = '';

        for ($index = $start; $index < $this->length; $index ++ ) {
            if ( ! in_range($index, 0, $end - 1)) {
                break;
            }
            $str .= $this->at($index);
        }


        return $this->withText($str);
    }

    /**
     * The split() method takes a pattern and divides a String into an ordered list of substrings by searching for the pattern
     */
    public function split(mixed $separator, int $limit = 0): iterable
    {

        $separator = $this->convert($separator);

        if (empty($separator) || $limit === 1) {
            return [$this];
        }

        if ( ! preg_valid($separator)) {
            $method = 'explode';
            $limit = $limit < 1 ? PHP_INT_MAX : $limit;
        } else { $method = 'preg_split'; }

        return map(fn($val) => $this->withText($val), $method($separator, $this->text, $limit));
    }

    /**
     * The substring() method returns the part of the string between the start and end indexes, or to the end of the string.
     *
     * @param int $start
     * @param int|null $end
     * @return static
     */
    public function substring(int $start = 0, ?int $end = null): static
    {

        if ($start === $end) {
            return $this->withText('');
        }

        if ( ! is_int($end)) {
            $end = $this->length;
        }

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }


        if ($end > $this->length) {
            $end = $this->length;
        }
        if ($start < 0) {
            $start = 0;
        }

        $str = '';
        for ($index = $start; $index < $end; $index ++ ) {
            $str .= $this->at($index);
        }


        return $this->withText($str);
    }

    /**
     * Left Trim the string of the given characters.
     */
    public function ltrim(mixed ...$chars): static
    {
        return $this->withText(ltrim(...array_merge([$this->text], $this->getChars($chars))));
    }

    /**
     * Alias of ltrim
     */
    public function trimStart(mixed ...$chars): static
    {
        return $this->ltrim(...$chars);
    }

    /**
     * Right Trim the string of the given characters.
     */
    public function rtrim(mixed ...$chars): static
    {

        return $this->withText(rtrim(...array_merge([$this->text], $this->getChars($chars))));
    }

    /**
     * Alias of rtrim
     */
    public function trimEnd(mixed ...$chars): static
    {
        return $this->rtrim(...$chars);
    }

    /**
     * Trim the string of the given characters.
     */
    public function trim(mixed ...$chars): static
    {
        return $this->withText(trim(...array_merge([$this->text], $this->getChars($chars))));
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== '';
    }

    public function offsetGet(mixed $offset): mixed
    {

        if (is_string($offset)) {

            if (preg_test('#^-?\d+$#', $offset)) {
                $offset = intval($offset);
            } elseif ($result = preg_exec('#^(-?\d+)?(?:\:(-?\d+)?)?(?:\:(-?\d+)?)?$#', $offset)) {

                @list(, $start, $stop, $step) = $result;

                if (is_null($step) || $step === '') {
                    $step = 1;
                }

                $step = intval($step);

                if ((string) $start === '') {
                    $start = $step > 0 ? 0 : -1;
                }

                if ((string) $stop === '') {
                    $stop = $step > 0 ? $this->length : -$this->length - 1;
                }


                $offset = new Range((int) $start, (int) $stop, $step);
            }
        }



        if ($offset instanceof Range && $this->isValidRange($offset)) {

            $str = '';
            $last = null;

            foreach ($offset as $index) {

                $sign = $index === 0 ? 1 : $index / abs($index);
                $last ??= $sign;
                if ($last !== $sign) {
                    break;
                }

                $str .= $this->at($index);

                $last = $sign;
            }
            return $str;
        }

        if (is_int($offset)) {
            return $this->at($offset) ?? '';
        }

        return '';
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
