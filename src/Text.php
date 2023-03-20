<?php

declare(strict_types=1);

namespace NGSOFT;

use Countable,
    IteratorAggregate;
use NGSOFT\{
    DataStructure\SimpleIterator, Traits\CloneUtils
};
use Stringable,
    Traversable;
use function in_range,
             mb_strlen,
             mb_strpos,
             mb_strtolower,
             mb_strtoupper,
             mb_substr,
             NGSOFT\Tools\every,
             preg_exec,
             preg_valid,
             str_val;

/**
 * A String manipulation utility that implements the best of Python and JavaScript
 */
class Text implements Stringable, Countable, IteratorAggregate
{

    use CloneUtils;

    public const DEFAULT_ENCODING = 'UTF-8';

    /**
     * Text to be managed
     *
     * @var string
     */
    protected string $text;

    /**
     * mb_default_encoding
     * @var string
     */
    protected string $encoding;

    /**
     * Hosts the real string length
     * @var int
     */
    protected int $length;

    /**
     * Hosts the byte size
     * @var int
     */
    protected int $size;

    /**
     * Hosts the multibyte char convertion map
     * @var int[]
     */
    protected array $map;

    /**
     * Construct a new Text instance
     */
    public static function of(mixed $text, string $encoding = self::DEFAULT_ENCODING): static
    {
        return new static($text, $encoding);
    }

    public function __construct(mixed $text, string $encoding = self::DEFAULT_ENCODING)
    {
        $this->encoding = $encoding;
        $this->initialize($text);
    }

    protected function initialize(mixed $text): static
    {
        $text = str_val($text);
        $this->map = [];
        $this->text = $text;
        $this->length = mb_strlen($text, $this->encoding);
        $this->size = strlen($text);

        return $this;
    }

    /**
     * get byte to multibyte char map
     */
    protected function getMap(): array
    {

        if (empty($this->map) && $this->length > 0)
        {

            if ($this->length === $this->size)
            {
                $this->map = range(0, $this->length - 1);
            }
            else
            {
                // build char map
                for ($offset = 0; $offset < $this->length; $offset ++ )
                {
                    $char = $this->at($offset);
                    for ($byte = 0; $byte < strlen($char); $byte ++ )
                    {
                        $this->map[] = $offset;
                    }
                }
            }
        }

        return $this->map;
    }

    protected function getByteOffset(int $offset): int
    {

        if (0 === $offset)
        {
            return $offset;
        }


        $result = array_search($offset, $this->getMap());
        return $result === false ? -1 : $result;
    }

    protected function getMultibyteOffset(int $offset): int
    {
        if (0 === $offset)
        {
            return $offset;
        }

        return $this->getMap()[$offset] ?? -1;
    }

    public function getIterator(): Traversable
    {

        if ($this->length > 0)
        {
            for ($offset = 0; $offset < $this->length; $offset ++ )
            {

                yield $this->at($offset);
            }
        }
    }

    public function count(mixed $value = null): int
    {
        if (null === $value)
        {
            return $this->length;
        }


        $value = str_val($value);

        $count = $offset = 0;

        while ($offset < $this->length)
        {
            [$str, $offset] = $this->indexOfTuple($value, $offset);

            if ($offset === -1)
            {
                break;
            }

            $count ++;
            $offset += mb_strlen($str, $this->encoding);
        }
        return $count;
    }

    public function __toString(): string
    {
        return $this->text;
    }

    /**
     * Checks if empty text
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Returns current text
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Returns encoding
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Returns multibyte length
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Returns byte size
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get a new instance with given text
     */
    public function withText(mixed $text): static
    {
        return $this->clone()->initialize($text);
    }

    /**
     * Get a new instance with specified encoding
     */
    public function withEncoding(string $encoding): static
    {
        return $this->with(encoding: $encoding)->initialize($this->text);
    }

    ////////////////////////////   JS Implementation (with some modifications)   ////////////////////////////

    /**
     * returns a tuple [string, int]
     */
    protected function indexOfTuple(string $needle, int $offset): array
    {

        static $failure = ['', -1];

        // regex?
        if (preg_valid($needle))
        {

            if (preg_match($needle, $this->text, $matches, PREG_OFFSET_CAPTURE, $offset))
            {

                [$str, $offset] = $matches[0];

                return [$str, $this->getMultibyteOffset($offset)];
            }

            return $failure;
        }
        elseif (false === $result = mb_strpos($this->text, $needle, $offset, $this->encoding))
        {
            return $failure;
        }

        return [$needle, $result];
    }

    /**
     * transforms negative offsets into positive ones
     */
    protected function translateOffset(int $offset): int
    {

        if ($offset < 0)
        {
            $offset += $this->length;
        }

        return $offset;
    }

    /**
     * The indexOf() method, given one argument: a substring/regex to search for,
     * searches the entire calling string, and returns the index of the first occurrence of the specified substring, -1 on failure
     */
    public function indexOf(mixed $needle, int $offset = 0): int
    {

        $needle = str_val($needle);

        if ($needle === '' || $this->isEmpty() || $offset > $this->length)
        {
            return -1;
        }

        return $this->indexOfTuple($needle, $offset)[1];
    }

    /**
     * Alias of indexOf
     */
    public function search(mixed $needle): int
    {
        return $this->indexOf($needle);
    }

    /**
     * The lastIndexOf() method, given one argument: a substring/regex to search for,
     * searches the entire calling string, and returns the index of the last occurrence of the specified substring, -1 on failure
     */
    public function lastIndexOf(mixed $needle, int $offset = PHP_INT_MAX): int
    {

        if ($needle === '' || $this->isEmpty() || $offset < 0)
        {
            return -1;
        }


        $result = -1;
        $pos = 0;

        while ($pos < $this->length)
        {
            [$str, $index] = $this->indexOfTuple($needle, $pos);

            if ($index === -1 || $index > $offset)
            {
                break;
            }

            $result = $index;
            $pos = $index + mb_strlen($str, $this->encoding);
        }


        return $result;
    }

    /**
     * The at() method takes an integer value and returns the character located at the specified offset
     */
    public function at(int $offset = 0): string
    {

        $offset = $this->translateOffset($offset);

        if ( ! in_range($offset, 0, $this->length - 1))
        {
            return '';
        }

        return mb_substr($this->text, $offset, 1, $this->encoding);
    }

    /**
     * The concat() method concatenates the string arguments to the current Text and returns a new instance
     */
    public function concat(mixed ...$strings): static
    {
        $str = $this->text;
        foreach ($strings as $string)
        {
            $str .= str_val($string);
        }

        return $this->withText($str);
    }

    /**
     * Converts Text to lower case
     */
    public function toLowerCase(): static
    {
        return $this->withText(mb_strtolower($this->text, $this->encoding));
    }

    /**
     * Converts Text to upper case
     */
    public function toUpperCase(): static
    {
        return $this->withText(mb_strtoupper($this->text, $this->encoding));
    }

    /**
     * Returns a tuple of arguments
     */
    protected function caselessArgs(string $needle, bool $caseless): array
    {
        if ($caseless)
        {
            return [mb_strtolower($this->text, $this->encoding), mb_strtolower($needle, $this->encoding)];
        }

        return [$this->text, $needle];
    }

    /**
     * The endsWith() method determines whether a string ends with the characters of a specified string, returning true or false as appropriate.
     */
    public function endsWith(mixed $needle, bool $caseless = false): bool
    {
        if ('' === $needle = str_val($needle))
        {
            return false;
        }


        return str_ends_with(...$this->caselessArgs($needle, $caseless));
    }

    /**
     * The startsWith() method determines whether a string begins with the characters of a specified string, returning true or false as appropriate.
     */
    public function startsWith(mixed $needle, bool $caseless = false): bool
    {
        if ('' === $needle = str_val($needle))
        {
            return false;
        }

        return str_starts_with(...$this->caselessArgs($needle, $caseless));
    }

    /**
     * The contains() method performs a search to determine whether one string may be found within another string/regex,
     * returning true or false as appropriate.
     */
    public function contains(mixed $needle, bool $caseless = false): bool
    {
        if ('' === $needle = str_val($needle))
        {
            return false;
        }

        if (preg_valid($needle))
        {
            return preg_match($needle, $this->text) > 0;
        }

        return str_contains(...$this->caselessArgs($needle, $caseless));
    }

    /**
     * The includes() method performs a case-sensitive search to determine whether one string may be found within another string, returning true or false as appropriate.
     */
    public function includes(mixed $needle): bool
    {
        return $this->contains($needle);
    }

    /**
     * The containsAll() method performs a search to determine whether one string may be found within multiple other string/regex,
     * returning true or false as appropriate.
     */
    public function containsAll(iterable $needles, bool $caseless = false): bool
    {
        return every(fn($needle) => $this->contains($needle, $caseless), $needles);
    }

    /**
     * The match() method retrieves the result of matching a string against a regular expression.
     */
    public function match(string $pattern): array
    {
        return preg_exec($pattern, $this->text);
    }

    /**
     * The matchAll() method returns an iterator of all results matching a string against a regular expression, including capturing groups.
     */
    public function matchAll(string $pattern): \Traversable
    {
        return SimpleIterator::of(preg_exec($pattern, $this->text, 0), true);
    }

    /**
     * Utility function for pads
     */
    protected function getPadding(int $length, string $padString): string
    {

        if ($padString === '' || $length < 1)
        {
            return '';
        }

        $str = '';
        while (mb_strlen($str, $this->encoding) < $length)
        {
            $str .= $padString;
        }

        return mb_substr($str, 0, $length, $this->encoding);
    }

    /**
     * The padStart() method pads the current string with another string (multiple times, if needed) until the resulting string reaches the given length.
     * The padding is applied from the start of the current string.
     */
    public function padStart(int $targetLength, mixed $padString = ' '): static
    {
        $length = $this->length - $targetLength;
        $padString = str_val($padString);

        if ($length < 1 || '' === $padString)
        {
            return $this;
        }


        return $this->withText($this->getPadding($length, $padString) . $this->text);
    }

    /**
     * The padEnd() method pads the current string with a given string (repeated, if needed) so that the resulting string reaches a given length.
     * The padding is applied from the end of the current string.
     */
    public function padEnd(int $targetLength, mixed $padString = ' '): static
    {
        $length = $this->length - $targetLength;
        $padString = str_val($padString);

        if ($length < 1 || '' === $padString)
        {
            return $this;
        }


        return $this->withText($this->text . $this->getPadding($length, $padString));
    }

    /**
     * The padEnd() method pads the current string with a given string (repeated, if needed) so that the resulting string reaches a given length.
     * The padding is applied to the start and the end of the current string.
     */
    public function pad(int $targetLength, mixed $padString = ' '): static
    {

        $length = $this->length - $targetLength;
        $padString = str_val($padString);

        if ($length < 1 || '' === $padString)
        {
            return $this;
        }

        $endPad = $this->length + intval(ceil($length / 2));

        return $this->padEnd($endPad, $padString)->padStart($targetLength, $padString);
    }

    /**
     * The repeat() method constructs and returns a new string which contains the specified number of copies of the string on which it was called,
     * concatenated together.
     */
    public function repeat(int $times): static
    {

        if ($times < 2)
        {
            return $this;
        }

        $str = '';

        for ($i = 0; $i < $times; $i ++ )
        {
            $str .= $this->text;
        }

        return $this->withText($str);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     */
    public function replace(mixed $search, mixed $replacement): static
    {

        [$str, $index] = $this->indexOfTuple($search = str_val($search), 0);

        if (-1 === $index)
        {
            return $this;
        }

        $replacement = str_val($replacement);

        return $this->withText(
                        mb_substr($this->text, 0, $index, $this->encoding) .
                        $replacement .
                        mb_substr($this->text, $index + mb_strlen($str, $this->encoding), encoding: $this->encoding)
        );
    }

    /**
     * The replaceAll() method returns a new string with all matches of a pattern replaced by a replacement.
     * The pattern can be a string or a RegExp, and the replacement can be a string or a function to be called for each match.
     */
    public function replaceAll(mixed $search, mixed $replacement): static
    {

        $search = str_val($search);

        if ($search === '')
        {
            return $this;
        }

        if ($replacement instanceof \Closure === false)
        {
            $replacement = str_val($replacement);
        }



        if (preg_valid($search))
        {

            if ($replacement instanceof \Closure)
            {
                return $this->withText(preg_replace_callback($search, $replacement, $this->text));
            }
            return $this->withText(preg_replace($search, $replacement, $this->text));
        }


        return $this->withText(str_replace($search, value($replacement, $search), $this->text));
    }

}
