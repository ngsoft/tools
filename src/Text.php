<?php

declare(strict_types=1);

namespace NGSOFT;

use ArrayAccess,
    Closure,
    Countable,
    IteratorAggregate,
    JsonSerializable;
use NGSOFT\{
    DataStructure\SimpleIterator, Text\Slice, Traits\CloneUtils
};
use OutOfRangeException,
    Stringable,
    Traversable;
use const MB_CASE_TITLE;
use function in_range,
             mb_convert_case,
             mb_str_split,
             mb_strlen,
             mb_strpos,
             mb_strtolower,
             mb_strtoupper,
             mb_substr,
             NGSOFT\Tools\every,
             preg_exec,
             preg_test,
             preg_valid,
             str_val,
             value;

/**
 * A String manipulation utility that implements the best of Python and JavaScript
 */
class Text implements Stringable, Countable, IteratorAggregate, ArrayAccess, JsonSerializable
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

    public function __serialize(): array
    {
        return [$this->text, $this->encoding];
    }

    public function __unserialize(array $data): void
    {
        $this->encoding = $data[1];
        $this->initialize($data[0]);
    }

    public function __debugInfo(): array
    {

        return [
            'text' => $this->text,
            'normalized' => trim(json_encode($this->text, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS), '"')
        ];
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
                for ($offset = 0; $offset < $this->length; $offset ++)
                {
                    $char = $this->at($offset);
                    for ($byte = 0; $byte < strlen($char); $byte ++)
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
            for ($offset = 0; $offset < $this->length; $offset ++)
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

    public function jsonSerialize(): mixed
    {
        return $this->toString();
    }

    /**
     * String representation of Text
     */
    public function toString(): string
    {
        return $this->text;
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
     * Get a new instance with given merged texts
     */
    public function withText(mixed ...$text): static
    {
        if (empty($text))
        {
            $text = '';
        }
        else
        {
            $text = implode('', array_map(fn($mixed) => str_val($mixed), $text));
        }


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
     * The valueOf() method returns the primitive value of a Text object
     */
    public function valueOf(): string
    {
        return $this->text;
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

        if ($this->isEmpty() || ! in_range($offset, 0, $this->length - 1))
        {
            return '';
        }

        return mb_substr($this->text, $offset, 1, $this->encoding);
    }

    /**
     * The charAt() method takes an integer value and returns the character located at the specified offset as a text instance
     */
    public function charAt(int $offset = 0): static
    {
        return $this->withText($this->at($offset));
    }

    /**
     * The concat() method concatenates the string arguments to the current Text and returns a new instance
     */
    public function concat(mixed ...$strings): static
    {
        return $this->withText($this->text, ...$strings);
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
        if ($this->isEmpty())
        {
            return false;
        }

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
        if ($this->isEmpty())
        {
            return false;
        }

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
        if ($this->isEmpty())
        {
            return false;
        }

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

        for ($i = 0; $i < $times; $i ++)
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

        [$str, $index] = $this->indexOfTuple(str_val($search), 0);

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

        if ($replacement instanceof Closure === false)
        {
            $replacement = str_val($replacement);
        }

        if (preg_valid($search))
        {

            if ($replacement instanceof Closure)
            {
                return $this->withText(preg_replace_callback($search, $replacement, $this->text));
            }
            return $this->withText(preg_replace($search, $replacement, $this->text));
        }


        return $this->withText(str_replace($search, value($replacement, $search), $this->text));
    }

    /**
     * The substring() method returns the part of the string from the start index up to and excluding the end index,
     * or to the end of the string if no end index is supplied.
     */
    public function substring(int $indexStart, ?int $indexEnd = null): static
    {
        if (is_null($indexEnd))
        {
            $indexEnd = $this->length;
        }


        if ($indexStart === $indexEnd)
        {
            return $this->withText('');
        }

        if ($indexStart > $indexEnd)
        {
            [$indexEnd, $indexStart] = [$indexStart, $indexEnd];
        }

        $str = '';
        for ($i = $indexStart; $i < $indexEnd; $i ++)
        {

            if ($i >= $this->length)
            {
                break;
            }

            if ($i < 0)
            {
                continue;
            }

            $str .= $this->at($i);
        }


        return $this->withText($str);
    }

    /**
     * The slice() method extracts a section of a string and returns it as a new string
     */
    public function slice(int $indexStart, ?int $indexEnd = null): static
    {
        if (is_null($indexEnd))
        {
            $indexEnd = $this->length;
        }

        [$indexStart, $indexEnd] = [$this->translateOffset($indexStart), $this->translateOffset($indexEnd)];

        if ($indexEnd <= $indexStart)
        {
            return $this->withText('');
        }


        $str = '';
        for ($i = $indexStart; $i < $indexEnd; $i ++)
        {

            if ($i >= $this->length)
            {
                break;
            }

            if ($i < 0)
            {
                continue;
            }

            $str .= $this->at($i);
        }


        return $this->withText($str);
    }

    /**
     * Helper method for trim
     */
    protected function getChars(mixed ...$chars): string
    {

        if (empty($chars))
        {
            // trim default value
            return " \n\r\t\v\x00";
        }

        return implode('', array_map(fn(mixed $char): string => str_val($char), $chars));
    }

    /**
     * The trim() method removes whitespace from both ends of a string and returns a new string
     */
    public function trim(mixed ...$chars): static
    {
        return $this->withText(trim($this->text, $this->getChars(...$chars)));
    }

    /**
     * The trimStart() method removes whitespace from the beginning of a string and returns a new string
     */
    public function trimStart(mixed ...$chars): static
    {
        return $this->withText(ltrim($this->text, $this->getChars(...$chars)));
    }

    /**
     * The trimEnd() method removes whitespace from the end of a string and returns a new string
     */
    public function trimEnd(mixed ...$chars): static
    {
        return $this->withText(rtrim($this->text, $this->getChars(...$chars)));
    }

    /**
     * The split() method takes a pattern and divides a String into an ordered list of substrings by searching for the pattern,
     *  puts these substrings into an array, and returns the array.
     */
    public function split(mixed $separator = '', int $limit = PHP_INT_MAX): array
    {
        if ($limit <= 0)
        {
            return [];
        }

        $result = [];
        $separator = str_val($separator);

        if ($separator === '')
        {
            $result = mb_str_split($this->text, encoding: $this->encoding);
        }
        elseif (preg_valid($separator))
        {
            $result = preg_split($separator, $this->text);
        }
        else
        {
            $result = explode($separator, $this->text);
        }

        while (count($result) > $limit)
        {
            array_pop($result);
        }
        return array_map(fn(string $str) => $this->withText($str), $result);
    }

    ////////////////////////////   Python like methods (the ones that are not duplicates of JS)   ////////////////////////////

    /**
     * Return a copy of the string with its first character capitalized and the rest lowercased.
     */
    public function capitalize(): static
    {

        if ($this->isEmpty())
        {
            return $this;
        }

        return $this->withText($this->charAt()->toUpperCase(), $this->slice(1)->toLowerCase());
    }

    /**
     * Return a copy of the string where all tab characters are replaced by one or more spaces
     */
    public function expandTabs(int $tabsize = 8): static
    {
        return $this->replaceAll('/\t/', $this->getPadding($tabsize, ' '));
    }

    /**
     * Return True if all characters in the string are alphanumeric and there is at least one character, False otherwise
     */
    public function isAlnum(): bool
    {
        return ctype_alnum($this->text);
    }

    /**
     * Return True if all characters in the string are alphabetic and there is at least one character, False otherwise.
     */
    public function isAlpha(): bool
    {
        return ctype_alpha($this->text);
    }

    /**
     * Return True if all characters in the string are decimal characters and there is at least one character, False otherwise
     */
    public function isDecimal(): bool
    {
        return preg_test('#^\d+$#', $this->text);
    }

    /**
     * Return True if all characters in the string are digits and there is at least one character, False otherwise.
     */
    public function isDigit(): bool
    {
        return ctype_digit($this->text);
    }

    /**
     * Return True if all cased characters in the string are lowercase and there is at least one cased character, False otherwise.
     */
    public function isLower(): bool
    {
        return preg_test('#[a-z]#', $this->text) && ! preg_test('#[A-Z]#', $this->text);
    }

    /**
     * Finds whether a variable is a number or a numeric string
     */
    public function isNumeric(): bool
    {
        return is_numeric($this->text);
    }

    /**
     * Return a titlecased version of the string where words start with an uppercase character and the remaining characters are lowercase.
     */
    public function title(): static
    {
        return $this->withText(mb_convert_case($this->text, MB_CASE_TITLE, $this->encoding));
    }

    /**
     * Return True if the string is a titlecased string and there is at least one character,
     * for example uppercase characters may only follow uncased characters and lowercase characters only cased ones.
     * Return False otherwise.
     */
    public function isTitle(): bool
    {
        return preg_test('#[A-Z]#', $this->text) && $this->title()->toString() === $this->text;
    }

    /**
     * Return True if there are only whitespace characters in the string and there is at least one character, False otherwise.
     */
    public function isSpace(): bool
    {
        return ctype_space($this->text);
    }

    /**
     * Return True if all characters in the string are printable or the string is empty, False otherwise.
     */
    public function isPrintable(): bool
    {
        return $this->isEmpty() || ctype_print($this->text);
    }

    /**
     * Checks if all of the characters in the provided Text,  are punctuation character.
     */
    public function isPunct(): bool
    {
        return ctype_punct($this->text);
    }

    /**
     * Checks if all characters in Text are control characters
     */
    public function isControl(): bool
    {
        return ctype_cntrl($this->text);
    }

    /**
     * Return True if all characters in the string are uppercase and there is at least one lowercase character, False otherwise.
     */
    public function isUpper(): bool
    {
        return ! preg_test('#[a-z]#', $this->text) && preg_test('#[A-Z]#', $this->text);
    }

    /**
     * If the string starts with the prefix string,
     * return string[len(prefix):]. Otherwise, return a copy of the original string:
     */
    public function removePrefix(mixed $prefix): static
    {
        if ($this->isEmpty() || empty($prefix = str_val($prefix)))
        {
            return $this;
        }




        if ($this->startsWith($prefix))
        {
            return $this->slice(mb_strlen($prefix, $this->encoding));
        }

        return $this;
    }

    /**
     * If the string ends with the suffix string and that suffix is not empty, return string[:-len(suffix)].
     * Otherwise, return a copy of the original string:
     */
    public function removeSuffix(mixed $suffix): static
    {
        if ($this->isEmpty() || empty($suffix = str_val($suffix)))
        {
            return $this;
        }

        if ($this->endsWith($suffix))
        {
            return $this->slice(0, - mb_strlen($suffix, $this->encoding));
        }

        return $this;
    }

    /**
     * Reverse the string
     */
    public function reverse(): static
    {

        if ($this->isEmpty())
        {
            return $this;
        }

        $str = '';

        for ($i = -1; $i >= -$this->length; $i --)
        {
            $str .= $this->at($i);
        }
        return $this->withText($str);
    }

    /**
     * Return a copy of the string with uppercase characters converted to lowercase and vice versa.
     */
    public function swapCase(): static
    {

        $text = $this->text;
        return $this->withText(mb_strtolower($text, $this->encoding) ^ mb_strtoupper($text, $this->encoding) ^ $text);
    }

    ////////////////////////////   PHP Methods   ////////////////////////////

    /**
     * Use sprintf to format string
     *
     * @phan-suppress PhanPluginPrintfVariableFormatString
     */
    public function format(mixed ...$args): static
    {

        if (count($args) && $this->indexOf('%') > -1)
        {
            return $this->withText(sprintf($this->text, ...$args));
        }

        return $this;
    }

    /**
     * Use ucfirst on the string
     */
    public function upperFirst(): static
    {
        if ($this->isEmpty())
        {
            return $this;
        }

        return $this->withText($this->charAt(0)->toUpperCase(), $this->slice(1));
    }

    /**
     * Use lcfirst on the string
     */
    public function lowerFirst(): static
    {
        if ($this->isEmpty())
        {
            return $this;
        }

        return $this->withText($this->charAt(0)->toLowerCase(), $this->slice(1));
    }

    /**
     * Returns new Text with suffix added
     */
    public function append(mixed ...$suffix): static
    {
        return $this->concat(...$suffix);
    }

    /**
     * Returns new Text with prefix added
     */
    public function prepend(mixed ...$prefix): static
    {
        $prefix[] = $this->text;
        return $this->withText(...$prefix);
    }

    /**
     * Checks if Text is base 64 encoded
     */
    public function isBase64(): bool
    {

        if ($this->isEmpty())
        {
            return false;
        }

        $b64 = @base64_decode($this->text, true);

        return $b64 !== false && @base64_encode($b64) === $this->text;
    }

    /**
     * Checks if string is hexadecimal number
     */
    public function ishexadecimal(): bool
    {
        return ctype_xdigit($this->text);
    }

    /**
     * Returns a base64 encoded Text
     */
    public function toBase64(): static
    {
        return $this->withText(base64_encode($this->text));
    }

    /**
     * Returns a base64 decoded Text
     */
    public function decodeBase64(): static
    {

        if ($this->isBase64())
        {
            return $this->withText(base64_decode($this->text));
        }
        return $this;
    }

    /**
     * Checks if needle equals current text
     */
    public function isEqual(mixed $needle, bool $caseless = false): bool
    {
        $needle = str_val($needle);
        if ($caseless)
        {
            return mb_strtolower($needle, $this->encoding) === $this->toLowerCase()->toString();
        }

        return $needle === $this->text;
    }

    ////////////////////////////   ArrayAccess/Slices   ////////////////////////////

    public function offsetExists(mixed $offset): bool
    {
        return ! $this->offsetGet($offset)->isEmpty();
    }

    public function offsetGet(mixed $offset): static
    {
        if (is_numeric($offset))
        {
            $offset = intval($offset);
        }

        if (is_string($offset))
        {
            if ( ! Slice::isValid($offset))
            {
                throw new OutOfRangeException(sprintf('Offset %s does not exists', $offset));
            }
            $offset = Slice::of($offset);
        }
        elseif (is_int($offset))
        {
            return $this->charAt($offset);
        }

        // we can also use slice instances directly as offsets

        if ($offset instanceof Slice)
        {
            $str = '';

            foreach ($offset->getIteratorFor($this) as $index)
            {
                $str .= $this->at($index);
            }

            return $this->withText($str);
        }

        // empty text
        return $this->withText('');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {

        // we cannot use slice there, that would be illogic (5:10:2 where to replace?)
        if (is_numeric($offset))
        {
            $offset = intval($offset);
        }
        elseif (is_null($offset))
        {
            $offset = $this->length;
        }

        if ( ! is_int($offset))
        {
            throw new OutOfRangeException('Offset does not exists');
        }
        $offset = $this->translateOffset($offset);

        if ($offset < 0)
        {
            return;
        }

        $value = str_val($value);

        if ($offset >= $this->length)
        {
            // we add spaces until offset is reached
            $str = $this->getPadding($offset - $this->length, ' ') . $value;
            $this->initialize($this->text . $str);
        }
        else
        {
            // we insert value at offset removing only the char contained at that offset
            $this->initialize($this->slice(0, $offset)->toString() . $value . $this->slice($offset + 1)->toString());
        }
    }

    public function offsetUnset(mixed $offset): void
    {

        if (is_numeric($offset))
        {
            $offset = intval($offset);
        }


        if (is_string($offset))
        {
            if ( ! Slice::isValid($offset))
            {
                throw new OutOfRangeException(sprintf('Offset %s does not exists', $offset));
            }
            $offset = Slice::of($offset);
        }
        elseif (is_int($offset))
        {
            $offset = $this->translateOffset($offset);

            if ($offset < 0 || $offset >= $this->length)
            {
                return;
            }
            // we remove the offset (it gets reassigned)
            $this->initialize($this->slice(0, $offset)->toString() . $this->slice($offset + 1)->toString());
        }

        // we remove the slice
        if ($offset instanceof Slice)
        {
            // we split the text
            $segments = mb_str_split($this->text, encoding: $this->encoding);

            foreach ($offset->getIteratorFor($this) as $index)
            {
                // and removes the offsets one per one
                unset($segments[$index]);
            }


            $this->initialize(implode('', $segments));
        }
    }

}
