<?php

declare(strict_types=1);

namespace NGSOFT;

use ErrorException,
    InvalidArgumentException,
    JsonSerializable,
    NGSOFT\Exceptions\RegExpException,
    Stringable,
    Traversable,
    TypeError;
use function get_debug_type,
             mb_str_split,
             mb_strlen,
             mb_substr,
             str_starts_with;

/**
 * @property int $lastIndex
 */
class RegExp implements Stringable, JsonSerializable
{

    /**
     * Most used php delimiters
     */
    public const PCRE_DELIMITERS = '%#/';

    /**
     * Case-insensitive search.
     */
    public const PCRE_CASELESS = 'i';

    /**
     * Multi-line search.
     */
    public const PCRE_MULTILINE = 'm';

    /**
     * Allows . to match newline characters.
     */
    public const PCRE_DOTALL = 's';

    /**
     * If this modifier is set, whitespace data characters in the pattern are totally ignored except when escaped or inside a character class,
     * and characters between an unescaped # outside a character class and the next newline character, inclusive, are also ignored.
     */
    public const PCRE_EXTENDED = 'x';

    /**
     * If this modifier is set, the pattern is forced to be "anchored", that is,
     * it is constrained to match only at the start of the string which is being searched (the "subject string").
     */
    public const PCRE_ANCHORED = 'A';

    /**
     * If this modifier is set, a dollar metacharacter in the pattern matches only at the end of the subject string.
     * Without this modifier, a dollar also matches immediately before the final character if it is a newline (but not before any other newlines).
     * This modifier is ignored if m modifier is set.
     */
    public const PCRE_DOLLAR_ENDONLY = 'D';

    /**
     * This modifier inverts the "greediness" of the quantifiers so that they are not greedy by default,
     * but become greedy if followed by ?.
     */
    public const PCRE_UNGREEDY = 'u';

    /**
     * Global search.
     */
    public const PCRE_GLOBAL = 'g';

    /**
     * Accepted Flags
     * @var string[]
     */
    private const ACCEPTED_FLAGS = [
        self::PCRE_CASELESS,
        self::PCRE_MULTILINE,
        self::PCRE_DOTALL,
        self::PCRE_EXTENDED,
        self::PCRE_ANCHORED,
        self::PCRE_DOLLAR_ENDONLY,
        self::PCRE_UNGREEDY,
    ];

    public readonly string $source;
    public readonly string $flags;
    public readonly bool $dotAll;
    public readonly bool $global;
    public readonly bool $ignoreCase;
    public readonly bool $multiline;
    public int $lastIndex = 0;

    /** @var string[] */
    private array $modifiers = [];
    private bool $isGlobal = false;
    private $tested = false;
    private $last = '';

    /**
     * Initialize RegExp
     *
     * @param string $pattern full pattern or pattern without delimitters and modifiers
     * @param string|string[] $flags modifiers
     * @return static
     */
    public static function create(string $pattern, string|array $flags = ''): static
    {
        return new static($pattern, $flags);
    }

    /**
     * Initialize RegExp
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp Same as that except pattern is a string
     *
     * @param string $pattern full pattern or pattern without delimitters and modifiers
     * @param string|string[] $flags modifiers
     */
    public function __construct(
            string $pattern,
            string|array $flags = ''
    )
    {

        if (is_string($flags)) $flags = mb_str_split($flags);
        $cleanPattern = $pattern;

        foreach (str_split(self::PCRE_DELIMITERS) as $delimiter) {
            if (str_starts_with($pattern, $delimiter)) {
                if (preg_match(sprintf('/[\%s]([a-z]*)$/i', $delimiter), $pattern, $matches) > 0) {
                    list($suffix, $addedFlags) = $matches;
                    $cleanPattern = mb_substr($pattern, 1);
                    $cleanPattern = mb_substr($cleanPattern, 0, mb_strlen($cleanPattern) - mb_strlen($suffix));

                    if ( ! empty($addedFlags)) $flags = array_merge($flags, mb_str_split($addedFlags));
                    break;
                }
                throw new InvalidArgumentException(sprintf('Invalid Pattern "%s" delimiters.', $pattern));
            }
        }

        $this->source = $cleanPattern;

        foreach ($flags as $flag) {
            if ($flag === self::PCRE_GLOBAL) {
                $this->isGlobal = true;
                continue;
            }
            if ( ! in_array($flag, self::ACCEPTED_FLAGS)) {
                throw new InvalidArgumentException(sprintf('Invalid flag "%s" (accepted: %s).', $flag, implode('', array_merge(self::ACCEPTED_FLAGS, [self::PCRE_GLOBAL]))));
            }
            $this->modifiers[$flag] = $flag;
        }

        $this->tested = false;
        $this->assertValidRegex();

        $this->global = $this->isGlobal;

        $this->flags = implode('', $this->modifiers) . ($this->isGlobal ? self::PCRE_GLOBAL : '');
        $this->dotAll = isset($flags[self::PCRE_DOTALL]);
        $this->ignoreCase = isset($flags[self::PCRE_CASELESS]);
        $this->multiline = isset($flags[self::PCRE_MULTILINE]);
    }

    ///////////////////////////////// Utils  /////////////////////////////////

    /**
     * Execute the preg method with the given arguments
     * @param callable $callback
     * @param array $arguments
     * @param mixed $expectedErrorReturnValue
     * @return mixed
     * @throws RegExpException
     */
    private function execute(callable $callback, array $arguments, mixed $expectedErrorReturnValue): mixed
    {

        Tools::errors_as_exceptions();
        try {
            $result = call_user_func_array($callback, $arguments);
        } catch (ErrorException $error) {
            throw new RegExpException($this, '', 0, $error);
        } finally {
            restore_error_handler();
        }
        if ($result === $expectedErrorReturnValue) {
            throw new RegExpException($this);
        }
        return $result;
    }

    /**
     * Test if regex is valid
     *
     * @phan-suppress PhanParamSuspiciousOrder
     */
    private function assertValidRegex()
    {
        if ($this->tested) return;

        $regex = $this->getUsableRegex();
        $method = $this->isGlobal ? 'preg_match_all' : 'preg_match';

        if (false === $method($regex, '')) {
            throw new RegExpException($this, sprintf('Invalid regex "%s".', $this->__toString()));
        }
        $this->tested = true;
    }

    /**
     * Get the regex string accepted by pcre functions
     * @return string
     */
    private function getUsableRegex(): string
    {
        return sprintf('#%s#%s', $this->source, implode('', $this->modifiers));
    }

    ////////////////////////////   Getters/Setters   ////////////////////////////

    /**
     * Get the last index
     * @return int
     */
    public function getLastIndex(): int
    {
        return $this->lastIndex;
    }

    /**
     * Set the Last Index
     * @param int $index
     * @return static
     */
    public function setLastIndex(int $index): self
    {
        $this->lastIndex = $index;
        return $this;
    }

    ////////////////////////////   Api   ////////////////////////////

    /**
     * The test() method executes a search for a match between a regular expression and a specified string.
     * @param string $str
     * @return bool
     */
    public function test(string $str): bool
    {
        $this->setLastIndex(0);
        $result = $this->exec($str);
        $this->setLastIndex(0);
        return $result !== null;
    }

    /**
     * The exec() method executes a search for a match in a specified string. Returns a result array, or null.
     * Will only gives the first result. if the global flag is set, the lastIndex from the previous match will be stored,
     * so you can loop through results (while loop).
     *
     * @phan-suppress PhanTypeInvalidDimOffset
     *
     * @param string $str  The string against which to match the regular expression
     * @return array|null If the match fails, the exec() method returns null, and sets lastIndex to 0.
     */
    public function exec(string $str): ?array
    {
        $this->assertValidRegex();
        $matches = [];
        $arguments = [
            $this->getUsableRegex(),
            $str,
            &$matches
        ];
        $stateful = false;
        if (
                $this->isGlobal === true
        ) {

            // reset index if using another search string
            if ($str !== $this->last) {
                $this->setLastIndex(0);
                $this->last = $str;
            }


            $stateful = true;
            $arguments[] = PREG_OFFSET_CAPTURE;
            $arguments[] = $this->getLastIndex();
        }



        if ($this->execute('preg_match', $arguments, false) > 0) {
            if ($stateful) {

                $result = array_map(fn($array) => $array[0], $matches);
                $this->setLastIndex($matches[0][array_key_last($matches[0])] + mb_strlen($result[0]));
            } else $result = $matches;
            return $result;
        }
        $this->setLastIndex(0);
        return null;
    }

    /**
     * The replace() method replaces some or all matches of a this pattern in a string by a replacement,
     * and returns the result of the replacement as a new string.
     * If global modifier is used all occurences will be replaced, else only the first occurence will be replaced.
     *
     * @suppress PhanRedundantCondition
     * @param string $str
     * @param string|Stringable|callable $replacement
     * @return string
     * @throws TypeError
     */
    public function replace(string $str, string|Stringable|callable $replacement): string
    {
        $this->assertValidRegex();
        if (
                ! is_callable($replacement) and
                ! is_string($replacement)
        ) {
            throw new TypeError(sprintf('Argument 2 passed to replace() must be of the type callable|string|Stringable, %s given.', get_debug_type($replacement)));
        }

        $replace = $replacement instanceof Stringable ? $replacement->__toString() : $replacement;

        $method = ! is_string($replace) ? 'preg_replace_callback' : 'preg_replace';
        $arguments = [
            $this->getUsableRegex(),
            $replace,
            $str,
            $this->isGlobal ? -1 : 1
        ];

        $result = $this->execute($method, $arguments, null);

        return $result;
    }

    /**
     * The search() method executes a search for a match between a regular expression and a string.
     *
     * @phan-suppress PhanTypeInvalidDimOffset
     * @param string $str
     * @return int The index of the first match between the regular expression and the given string, or -1 if no match was found.
     */
    public function search(string $str): int
    {
        $this->assertValidRegex();

        $matches = [];
        $arguments = [
            $this->getUsableRegex(),
            $str,
            &$matches,
            PREG_OFFSET_CAPTURE,
            $this->getLastIndex()
        ];

        if ($this->execute('preg_match', $arguments, false) > 0) {
            return $matches[0][array_key_last($matches[0])];
        }
        return -1;
    }

    /**
     * The split() method divides a String into an ordered list of substrings,
     *
     * @param string $str
     * @param int $limit
     * @return array
     */
    public function split(string $str, int $limit = -1): array
    {
        $this->assertValidRegex();
        return $this->execute('preg_split', [$this->getUsableRegex(), $str, $limit], false);
    }

    /**
     * The matchAll() method returns an iterator of all results matching a string against a regular expression, including capturing groups.
     * @param string $str
     * @return Traversable
     * @throws RegExpException
     * @suppress PhanSuspiciousValueComparison
     */
    public function matchAll(string $str): Traversable
    {
        $this->assertValidRegex();
        if ( ! $this->isGlobal) {
            throw new RegExpException($this, self::class . '::' . __FUNCTION__ . '() must be called when using the global flag(' . self::PCRE_GLOBAL . ').');
        }
        $matches = [];
        $arguments = [
            $this->getUsableRegex(),
            $str,
            &$matches,
            PREG_SET_ORDER,
            $this->getLastIndex()
        ];
        $this->setLastIndex(0);
        if ($this->execute('preg_match_all', $arguments, false) > 0) {

            for ($i = 0; $i < count($matches); $i ++) yield $i => $matches[$i];
        }
    }

    /**
     * The match() method retrieves the result of matching a string against a regular expression.
     * @param string $str
     * @return array|null
     */
    public function match(string $str): ?array
    {
        $this->assertValidRegex();
        if (
                ! $this->isGlobal
        ) return $this->exec($str);

        $this->setLastIndex(0);

        $matches = [];
        $arguments = [
            $this->getUsableRegex(),
            $str,
            &$matches,
            PREG_SET_ORDER
        ];

        if ($this->execute('preg_match_all', $arguments, false) > 0) {
            return array_map(fn($array) => $array[0], $matches);
        }
        return null;
    }

    ////////////////////////////   Magic Methods   ////////////////////////////

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {

        $this->__construct($data['source'], $data['flags']);
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {

        return [
            'source' => $this->source,
            'flags' => $this->flags,
        ];
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return $this->__toString();
    }

    /** {@inheritdoc} */
    public function __toString(): string
    {
        $regex = $this->getUsableRegex();
        if ($this->isGlobal) $regex .= self::PCRE_GLOBAL;
        return $regex;
    }

    public function __debugInfo(): array
    {
        return [
            'pattern' => $this->__toString(),
            'source' => $this->source,
            'flags' => $this->flags,
            'dotAll' => $this->dotAll,
            'global' => $this->global,
            'ignoreCase' => $this->ignoreCase,
            'multiline' => $this->multiline,
            'lastIndex' => $this->lastIndex
        ];
    }

}
