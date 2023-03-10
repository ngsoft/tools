<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    JsonSerializable;
use NGSOFT\{
    Tools, Tools\CharMap, Traits\SliceAble
};
use OutOfRangeException,
    Stringable,
    ValueError;
use const MB_CASE_TITLE;
use function is_arrayaccess,
             mb_convert_case,
             mb_str_split,
             mb_strlen,
             mb_strpos,
             mb_strtolower,
             mb_strtoupper,
             mb_substr,
             mb_substr_count;
use function NGSOFT\Tools\{
    every, map
};
use function preg_exec,
             preg_test,
             preg_valid,
             str_contains,
             str_ends_with,
             str_starts_with,
             str_val;

/**
 * Transform a scalar to its stringable representation
 * @phan-file-suppress PhanUnusedPublicMethodParameter, PhanParamTooFewInternalUnpack
 */
class Text implements Stringable, Countable, ArrayAccess, JsonSerializable
{

    use SliceAble;

    protected string $text;
    protected int $length;

    /**
     * Create new Text
     */
    public static function create(mixed $text): static
    {
        return static::of($text);
    }

    /**
     * Create new Text
     */
    public static function of(mixed $text): static
    {

        if ($text instanceof self)
        {
            return $text;
        }
        return new static($text);
    }

    /**
     * Create multiple segments of Text
     * @return static[]
     */
    public function ofSegments(mixed ...$segments): array
    {
        return map(fn($text) => static::create($text), $segments);
    }

    public function __construct(mixed $text = '')
    {
        $this->setText($text);
    }

    protected function convert(mixed $text): string
    {
        return str_val($text);
    }

    protected function setText(mixed $text): static
    {
        $this->text = $this->convert($text);
        $this->length = mb_strlen($this->text);

        return $this;
    }

    /**
     * Get a Text Copy
     */
    public function copy(): static
    {
        return clone $this;
    }

    protected function withText(mixed $text): static
    {
        return $this->copy()->setText($text);
    }

    protected function withSegments(mixed ...$segments): array
    {
        return map(fn($text) => $this->withText($text), $segments);
    }

    protected function translateOffset(int $offset): int
    {
        return $offset < 0 ? $this->length + $offset : $offset;
    }

    protected function getChars(array $chars): array
    {
        $result = [];

        foreach ($chars as $char)
        {
            $result[0] ??= '';
            $result[0] .= $this->convert($char);
        }
        return $result;
    }

    protected function _indexOf(string $needle, int $offset = 0): array
    {
        if (preg_valid($needle))
        {

            //translate char into byte offset

            if (-1 === $offset = CharMap::getByteOffset($this->text, $offset))
            {
                return [];
            }


            if (preg_match($needle, $this->text, $matches, PREG_OFFSET_CAPTURE, $offset))
            {

                // translate byte into char offset
                return [$matches[0][0], CharMap::getCharOffset($this->text, $matches[0][1])];
            }
        }
        elseif (is_int($pos = mb_strpos($this->text, $needle, $offset)))
        {
            return [$needle, $pos];
        }

        return [];
    }

    protected function _lastIndexOf(mixed $needle, int $offset = 0): array
    {

        $result = [];

        //  -1 !== $pos = $this->indexOf($needle, $offset)
        while ($offset < $this->length && count($indexOf = $this->_indexOf($needle, $offset)) > 0 && -1 !== $pos = $indexOf[1])
        {
            $result = $indexOf;
            $offset = $pos + mb_strlen($indexOf[0]);
        }

        return $result;
    }

    protected function getEmptyPartition(): array
    {
        static $empty;
        $empty ??= $this->withText('');
        return [$this->copy(), $empty, $empty];
    }

    ////////////////////////////   JS Like   ////////////////////////////

    /**
     * The indexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the first occurrence of the specified substring
     */
    public function indexOf(mixed $needle, int $offset = 0): int
    {
        $needle = $this->convert($needle);

        if ($needle === '' || $this->isEmpty())
        {
            return -1;
        }

        if ($offset >= $this->length)
        {
            return -1;
        }


        return $this->_indexOf($needle, $offset)[1] ?? -1;
    }

    /**
     * Alias of indexOf
     */
    public function search(mixed $needle)
    {
        return $this->indexOf($needle);
    }

    /**
     * The lastIndexOf() method, given one argument: a substring/regex to search for, searches the entire calling string, and returns the index of the last occurrence of the specified substring.
     */
    public function lastIndexOf(mixed $needle, int $offset = 0): int
    {
        return $this->_lastIndexOf($needle, $offset)[1] ?? -1;
    }

    /**
     * The at() method takes an integer value and returns the character located at the specified offset
     */
    public function at(int $offset = 0): string
    {


        $offset = $this->translateOffset($offset);

        if ($offset >= $this->length || $offset < 0)
        {
            return '';
        }

        return mb_substr($this->text, $offset, 1);
    }

    /**
     * The concat() method concatenates the string arguments to the current Text
     */
    public function concat(mixed ...$strings): static
    {
        $str = $this->text;
        foreach ($strings as $string)
        {
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

        if ('' === $needle)
        {
            return false;
        }

        $haystack = $this->text;

        if ($ignoreCase)
        {
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

        if ('' === $needle)
        {
            return false;
        }



        $haystack = $this->text;

        if ($ignoreCase)
        {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }


        return str_starts_with($haystack, $needle);
    }

    /**
     * The includes() method performs a search to determine whether one string may be found within another string/regex, returning true or false as appropriate.
     */
    public function contains(mixed $needle, bool $ignoreCase = false): bool
    {

        $needle = $this->convert($needle);

        if ($needle === '')
        {
            return false;
        }

        $haystack = $this->text;

        if ($ignoreCase)
        {
            $haystack = mb_strtolower($haystack);
            $needle = mb_strtolower($needle);
        }


        return str_contains($haystack, $needle);
    }

    /**
     * Determine if a given string contains all needles
     */
    public function containsAll(iterable $needles, bool $ignoreCase = false): bool
    {
        return every(fn($n) => $this->contains($n, $ignoreCase), $needles);
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
    public function padStart(int $length, mixed $pad = ' '): static
    {

        $total = $length;
        $length -= $this->length;

        if ($length <= 0 || '' === $pad = $this->convert($pad))
        {
            return $this;
        }

        $str = $this->text;

        while (mb_strlen($str) < $total)
        {
            $str = $pad . $str;
        }

        return $this->withText(mb_substr($str, $total - mb_strlen($str)));
    }

    /**
     * Pad the right side of a string with another.
     */
    public function padEnd(int $length, mixed $pad = ' '): static
    {

        $total = $length;

        $length -= $this->length;

        if ($length <= 0 || '' === $pad = $this->convert($pad))
        {
            return $this;
        }


        $str = $this->text;

        while (mb_strlen($str) < $total)
        {
            $str .= $pad;
        }

        return $this->withText(mb_substr($str, 0, $total));
    }

    /**
     * Pad on both sides of a string with another.
     */
    public function pad(int $length, mixed $pad = ' '): static
    {
        $total = $length;
        $length -= $this->length;

        if ($length <= 0 || '' === $pad = $this->convert($pad))
        {
            return $this;
        }

        $right = $this->length + intval(ceil($length / 2));

        return $this->padEnd($right, $pad)->padStart($total, $pad);
    }

    /**
     * The repeat() method constructs and returns a new string which contains the specified number of copies of the string on which it was called,
     * concatenated together.
     */
    public function repeat(int $times): static
    {

        $times = max(0, $times);
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

        $search = $this->convert($search);

        if ($search === '')
        {
            return $this->copy();
        }

        $len = mb_strlen($search);

        $replacement = $this->convert($replacement);

        $index = $this->indexOf($search);

        if ($index === -1)
        {
            return $this->copy();
        }

        $str = mb_substr($this->text, 0, $index);
        $str .= $replacement;
        $str .= mb_substr($this->text, $index + $len);
        return $this->withText($str);
    }

    public function replaceAll(mixed $search, mixed $replacement): static
    {

        if (is_iterable($search))
        {
            $result = $this;
            $index = 0;
            foreach ($search as $pattern)
            {
                if (is_arrayaccess($replacement))
                {
                    $replace = $replacement[$index] ?? '';
                }
                else
                { $replace = $replacement; }
                $result = $result->replaceAll($pattern, $replace);
                $index ++;
            }
            return $result;
        }

        $search = $this->convert($search);

        if ($search === '')
        {
            return $this;
        }

        $replacement = $this->convert($replacement);
        $method = preg_valid($search) ? 'preg_replace' : 'str_replace';

        return $this->withText($method($search, $replacement, $this->text));
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

        if ($start === $end)
        {
            return $this->withText('');
        }

        if ( ! is_int($end))
        {
            $end = $this->length;
        }

        if ($start > $end)
        {
            [$start, $end] = [$end, $start];
        }


        if ($end > $this->length)
        {
            $end = $this->length;
        }
        if ($start < 0)
        {
            $start = 0;
        }

        $str = '';
        for ($index = $start; $index < $end; $index ++ )
        {
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

    ////////////////////////////   Python Like Methods   ////////////////////////////

    /**
     * Return a copy of the string with its first character capitalized and the rest lowercased.
     */
    public function capitalize(): static
    {

        if ( ! $this->length)
        {
            return $this;
        }

        [$start, $end] = [$this->at(0), $this->slice(1)->toString()];

        $start = mb_strtoupper($start);
        $end = mb_strtolower($end);

        return $this->withText($start . $end);
    }

    /**
     * Return centered in a string of length width. Padding is done using the specified fillchar (default is an ASCII space).
     * The original string is returned if width is less than or equal to len(s).
     */
    public function center(int $width, mixed $fillchar = ' '): static
    {
        return $this->pad($width, $fillchar);
    }

    /**
     * Return a copy of the string where all tab characters are replaced by one or more spaces
     */
    public function expandtabs(int $tabsize = 8): static
    {

        $tabsize = max(0, $tabsize);

        return $this->replaceAll(["\t", '\t'], $tabsize > 0 ? str_repeat(' ', $tabsize) : '');
    }

    /**
     * Return the lowest index in the string where substring sub is found within the slice s[start:end].
     * Optional arguments start and end are interpreted as in slice notation. Return -1 if sub is not found.
     */
    public function find(mixed $sub, ?int $start = null, ?int $end = null): int
    {

        $sub = $this->convert($sub);
        $start ??= 0;
        $end ??= $this->length;

        return $this->slice($start, $end)->indexOf($sub);
    }

    /**
     * Perform a string formatting operation. The string on which this method is called can contain literal text or replacement fields delimited by braces {}.
     * Each replacement field contains either the numeric index of a positional argument, or the name of a keyword argument.
     */
    public function format(mixed ...$args): static
    {
        $result = $this;
        $replacements = $replaced = [];

        foreach ($args as $index => $arg)
        {
            $replacements[$index] = $this->convert($arg);
        }


        if ($matches = preg_exec("#{([^}]+)}#", $this->text, 0))
        {

            foreach ($matches as $matches)
            {

                [$sub, $offset] = $matches;

                if (isset($replaced[$sub]))
                {
                    continue;
                }

                $offset = is_numeric($offset) ? intval($offset) : $offset;

                if ( ! array_key_exists($offset, $replacements))
                {
                    throw new ValueError('Invalid key ' . $sub);
                }

                $result = $result->replaceAll($sub, $replacements[(int) $offset]);

                $replaced[$sub] = true;
            }
        }

        return $result;
    }

    /**
     * Like find(), but raise ValueError when the substring is not found.
     */
    public function index(mixed $sub, ?int $start = null, ?int $end = null): int
    {

        if (-1 === $result = $this->find($sub, $start, $end))
        {
            throw new ValueError('Substring not found.');
        }

        return $result;
    }

    /**
     * Return True if all characters in the string are alphanumeric and there is at least one character, False otherwise
     */
    public function isalnum(): bool
    {
        return ctype_alnum($this->text);
    }

    /**
     * Return True if all characters in the string are alphabetic and there is at least one character, False otherwise.
     */
    public function isalpha(): bool
    {
        return ctype_alpha($this->text);
    }

    /**
     * Return True if all characters in the string are decimal characters and there is at least one character, False otherwise
     */
    public function isdecimal(): bool
    {
        return preg_test('#^\d+$#', $this->text);
    }

    /**
     * Return True if all characters in the string are digits and there is at least one character, False otherwise.
     */
    public function isdigit(): bool
    {
        return ctype_digit($this->text);
    }

    /**
     * Return True if all cased characters in the string are lowercase and there is at least one cased character, False otherwise.
     */
    public function islower(): bool
    {
        return preg_test('#[a-z]#', $this->text) && ! preg_test('#[A-Z]#', $this->text);
    }

    /**
     * Finds whether a variable is a number or a numeric string
     * @link https://www.php.net/manual/en/function.is-numeric.php
     */
    public function isnumeric(): bool
    {
        return is_numeric($this->text);
    }

    /**
     * Return True if the string is a titlecased string and there is at least one character,
     * for example uppercase characters may only follow uncased characters and lowercase characters only cased ones.
     * Return False otherwise.
     */
    public function istitle(): bool
    {
        return preg_test('#[A-Z]#', $this->text) && $this->title()->toString() === $this->text;
    }

    /**
     * Return True if there are only whitespace characters in the string and there is at least one character, False otherwise.
     */
    public function isspace(): bool
    {
        return ctype_space($this->text);
    }

    /**
     * Return True if all characters in the string are printable or the string is empty, False otherwise.
     */
    public function isprintable(): bool
    {
        return $this->isEmpty() || ctype_print($this->text);
    }

    /**
     * Checks if all of the characters in the provided Text,  are punctuation character.
     */
    public function ispunct(): bool
    {
        return ctype_punct($this->text);
    }

    /**
     * Checks if all characters in Text are control characters
     */
    public function iscontrol(): bool
    {
        return ctype_cntrl($this->text);
    }

    /**
     * Return True if all characters in the string are uppercase and there is at least one lowercase character, False otherwise.
     */
    public function isupper(): bool
    {
        return ! preg_test('#[a-z]#', $this->text) && preg_test('#[A-Z]#', $this->text);
    }

    /**
     * Return a string which is the concatenation of the strings in iterable.
     * The separator between elements is the Text providing this method.
     */
    public function join(mixed $iterable): static
    {

        if ( ! is_iterable($iterable))
        {
            $iterable = mb_str_split($this->convert($iterable));
        }


        return $this->withText(Tools::join($this->text, $iterable));
    }

    /**
     * Return a copy of the string with all characters converted to lowercase.
     */
    public function lower(): static
    {
        return $this->toLowerCase();
    }

    /**
     * Return a copy of the string with leading characters removed.
     * The chars argument is a string specifying the set of characters to be removed. If omitted or null, the chars argument defaults to removing whitespace.
     * The chars argument is not a prefix; rather, all combinations of its values are stripped:
     */
    public function lstrip(mixed $chars = null): static
    {
        $args = [];
        if ( ! is_null($chars))
        {
            $args [] = $this->convert($chars);
        }

        return $this->ltrim(...$args);
    }

    /**
     * Split the string at the first occurrence of sep,
     * and return a 3-tuple containing the part before the separator, the separator itself, and the part after the separator.
     * If the separator is not found, return a 3-tuple containing the string itself, followed by two empty strings.
     */
    public function partition(mixed $sep): array
    {
        $sep = $this->convert($sep);

        if ($sep === '')
        {
            throw new ValueError('Empty separator.');
        }

        $result = $this->_indexOf($sep);
        if (-1 !== $index = $result[1] ?? -1)
        {
            $_sep = $this->withText($result[0]);
            return [$this->slice(0, $index), $_sep, $this->slice($index + count($_sep))];
        }

        return $this->getEmptyPartition();
    }

    /**
     * If the string starts with the prefix string,
     * return string[len(prefix):]. Otherwise, return a copy of the original string:
     */
    public function removeprefix(mixed $prefix): static
    {
        if ($this->isEmpty())
        {
            return $this;
        }


        if ($this->startsWith($prefix))
        {
            return $this->slice(mb_strlen($this->convert($prefix)));
        }

        return $this;
    }

    /**
     * If the string ends with the suffix string and that suffix is not empty, return string[:-len(suffix)].
     * Otherwise, return a copy of the original string:
     */
    public function removeSuffix(mixed $suffix): static
    {
        if ($this->isEmpty())
        {
            return $this;
        }
        if ($this->endsWith($suffix))
        {
            return $this->slice(end: - mb_strlen($this->convert($suffix)));
        }

        return $this;
    }

    /**
     * Reverse the string
     */
    public function reverse(): static
    {
        return $this->withText(implode('', array_reverse(mb_str_split($this->text))));
    }

    /**
     * Return the highest index in the string where substring sub is found, such that sub is contained within s[start:end].
     * Optional arguments start and end are interpreted as in slice notation.
     * Return -1 on failure.
     */
    public function rfind(mixed $sub, ?int $start = null, ?int $end = null): int
    {
        return $this->slice($start, $end)->lastIndexOf($sub);
    }

    /**
     * Like rfind() but raises ValueError when the substring sub is not found.
     */
    public function rindex(mixed $sub, ?int $start = null, ?int $end = null): int
    {
        if (-1 === $result = $this->rfind($sub, $start, $end))
        {
            throw new ValueError('Substring not found.');
        }

        return $result;
    }

    /**
     * Split the string at the last occurrence of sep,
     * and return a 3-tuple containing the part before the separator, the separator itself, and the part after the separator.
     * If the separator is not found, return a 3-tuple containing two empty strings, followed by the string itself.
     */
    public function rpartition(mixed $sep): array
    {

        $sep = $this->convert($sep);

        if ($sep === '')
        {
            throw new ValueError('Empty separator.');
        }


        $result = $this->_lastIndexOf($sep);
        if (-1 !== $index = $result[1] ?? -1)
        {
            $_sep = $this->withText($result[0]);
            return [$this->slice(0, $index), $_sep, $this->slice($index + count($_sep))];
        }

        return $this->getEmptyPartition();
    }

    /**
     * Return a copy of the string with trailing characters removed.
     * The chars argument is a string specifying the set of characters to be removed.
     * If omitted or None, the chars argument defaults to removing whitespace.
     * The chars argument is not a suffix; rather, all combinations of its values are stripped:
     */
    public function rstrip(mixed $chars = null): static
    {
        $args = [];
        if ( ! is_null($chars))
        {
            $args [] = $this->convert($chars);
        }

        return $this->rtrim(...$args);
    }

    /**
     * Return a copy of the string with the leading and trailing characters removed.
     * The chars argument is a string specifying the set of characters to be removed.
     * If omitted or None, the chars argument defaults to removing whitespace.
     * The chars argument is not a prefix or suffix; rather, all combinations of its values are stripped:
     */
    public function strip(mixed $chars = null): static
    {
        $args = [];
        if ( ! is_null($chars))
        {
            $args [] = $this->convert($chars);
        }

        return $this->trim(...$args);
    }

    /**
     * Return a copy of the string with uppercase characters converted to lowercase and vice versa.
     */
    public function swapcase(): static
    {
        return $this->withText(mb_strtolower($this->text) ^ mb_strtoupper($this->text) ^ $this->text);
    }

    /**
     * The slice() method extracts a section of a string and returns it as a new string
     */
    public function slice(?int $start = null, ?int $end = null, ?int $step = null): static
    {
        return $this->withText($this->joinSliceValue(Slice::create($start, $end, $step), $this));
    }

    /**
     * Return a titlecased version of the string where words start with an uppercase character and the remaining characters are lowercase.
     */
    public function title(): static
    {
        return $this->withText(mb_convert_case($this->text, MB_CASE_TITLE));
    }

    /**
     * Return a copy of the string with all the cased characters converted to uppercase
     */
    public function upper(): static
    {
        return $this->toUpperCase();
    }

    /**
     * Return a list of the words in the string, using sep as the delimiter string.
     * If maxsplit is given, at most maxsplit splits are done (thus, the list will have at most maxsplit+1 elements).
     * If maxsplit is not specified or -1, then there is no limit on the number of splits (all possible splits are made).
     *
     * @return static[]
     */
    public function split(mixed $sep = null, int $maxsplit = -1): array
    {
        $sep = $this->convert($sep);

        if ($sep === '')
        {
            throw new ValueError('Empty separator.');
        }


        if ($maxsplit === 0)
        {
            return [$this->copy()];
        }


        return $this->withSegments(...Tools::split($sep, $this->text, $maxsplit));
    }

    /**
     * Return a list of the words in the string, using sep as the delimiter string.
     * If maxsplit is given, at most maxsplit splits are done, the rightmost ones.
     * If sep is not specified or None, any whitespace string is a separator.
     * Except for splitting from the right, rsplit() behaves like split().
     */
    public function rsplit(mixed $sep = ' ', int $maxsplit = -1): array
    {

        if (-1 === $maxsplit)
        {
            return $this->split($sep);
        }

        $sep = $this->convert($sep);

        if ($sep === '')
        {
            throw new ValueError('Empty separator.');
        }

        if ($maxsplit === 0 || ! $this->contains($sep))
        {
            return [$this->copy()];
        }

        $result = [];

        $text = $this;

        while (count($result) < $maxsplit)
        {


            if ($resp = $text->_lastIndexOf($sep))
            {
                [$_sep, $offset] = $resp;
                $result[] = $text->slice($offset + mb_strlen($_sep));
                $text = $text->slice(0, $offset);
                continue;
            }

            break;
        }

        $result[] = $text;

        return array_reverse($result);
    }

    /**
     * Return a list of the lines in the string, breaking at line boundaries.
     * Line breaks are not included in the resulting list unless keepends is given and true.
     */
    public function splitlines(bool $keepends = false): array
    {

        if ($this->isEmpty() || ! preg_test('#\v+#', $this->text))
        {
            return [$this->copy()];
        }

        $result = [];

        $text = $this->text;

        while ( ! is_null($text))
        {

            @list($segment, $delim, $text) = preg_split('#(\v)#', $text, 2, PREG_SPLIT_DELIM_CAPTURE);

            if ( ! $keepends)
            {
                $delim = '';
            }

            $segment .= $delim ?? '';

            if (is_null($text) && $segment === '')
            {
                break;
            }

            $result[] = $segment;
        }

        return $this->withSegments(...$result);
    }

    ////////////////////////////   PHP Methods   ////////////////////////////

    /**
     * Use sprintf to format string
     *
     * @phan-suppress PhanPluginPrintfVariableFormatString
     */
    public function sprintf(mixed ...$args): static
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
    public function ucfirst(): static
    {
        if ($this->isEmpty())
        {
            return $this;
        }

        return $this->withText(mb_strtoupper($this->at(0)) . $this->slice(1)->toString());
    }

    /**
     * Use lcfirst on the string
     */
    public function lcfirst(): static
    {

        if ($this->isEmpty())
        {
            return $this;
        }
        return $this->withText(mb_strtolower($this->at(0)) . $this->slice(1)->toString());
    }

    /**
     * Returns new Text with suffix added
     */
    public function append(mixed ...$suffix): static
    {

        $text = '';
        foreach ($suffix as $_suffix)
        {
            $text .= $this->convert($_suffix);
        }


        return $this->withText($this->text . $text);
    }

    /**
     * Returns new Text with prefix added
     */
    public function prepend(mixed $prefix): static
    {
        $text = '';
        foreach ($prefix as $_prefix)
        {
            $text .= $this->convert($_prefix);
        }
        return $this->withText($text . $this->text);
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

        $b64 = base64_decode($this->text, true);

        return $b64 !== false && base64_encode($b64) === $this->text;
    }

    /**
     * Returns a base64 decoded Text
     */
    public function base64Encode(): static
    {
        return $this->withText(base64_encode($this->text));
    }

    /**
     * Returns a base64 decoded Text
     */
    public function base64Decode(): static
    {
        return $this->withText(base64_decode($this->text));
    }

    /**
     * Split the Text into multiple Text[]
     */
    public function splitChars(int $length = 1): array
    {
        if ($this->isEmpty())
        {
            return [$this->copy()];
        }

        return map(fn($char) => $this->withText($char), mb_str_split($this->text, max(1, $length)));
    }

    /**
     * Count needle occurences inside Text
     * if using a regex as needle the search will be case sensitive
     */
    public function countChars(mixed $needle, bool $ignoreCase = false): int
    {

        if ($this->isEmpty())
        {
            return 0;
        }

        [$haystack, $needle] = [$this->text, $this->convert($needle)];

        if (preg_valid($needle))
        {

            return (int) preg_match_all($needle, $haystack);
        }

        if ($ignoreCase)
        {
            [$haystack, $needle] = [mb_strtolower($haystack), mb_strtolower($needle)];
        }


        return mb_substr_count($haystack, $needle);
    }

    /**
     * Checks if Text is the same as the provided needle
     */
    public function isEquals(mixed $needle, bool $ignoreCase = false)
    {

        [$haystack, $needle] = [$this->text, $this->convert($needle)];

        if ($ignoreCase)
        {
            [$haystack, $needle] = [mb_strtolower($haystack), mb_strtolower($needle)];
        }

        return $haystack === $needle;
    }

    /**
     * Checks if string is hexadecimal number
     */
    public function ishexadecimal(): bool
    {
        return ctype_xdigit($this->text);
    }

    /**
     * Returns the length of the text
     */
    public function length(): int
    {
        return $this->count();
    }

    /**
     * Returns the byte size
     */
    public function size(): int
    {
        return strlen($this->text);
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    public function offsetExists(mixed $offset): bool
    {
        return ! $this->offsetGet($offset)->isEmpty();
    }

    /**
     * @return static
     */
    public function offsetGet(mixed $offset): static
    {

        if (is_numeric($offset))
        {
            $offset = intval($offset);
        }

        if (is_string($offset))
        {
            $offset = $this->getSlice($offset);
        }

        if ($offset instanceof Slice)
        {
            return $this->withText($this->joinSliceValue($offset, $this));
        }

        if (is_int($offset))
        {

            if ('' === $value = $this->at($offset))
            {
                throw new OutOfRangeException(sprintf('Offset #%d does not exists.', $offset));
            }


            return $this->withText($value);
        }



        return $this->withText('');
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
        return [$this->text, $this->length];
    }

    public function __unserialize(array $data): void
    {
        list($this->text, $this->length) = $data;
    }

    public function __debugInfo(): array
    {

        return [
            'text' => $this->text,
            'normalized' => trim(json_encode($this->text, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS), '"')
        ];
    }

}
