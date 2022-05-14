<?php

declare(strict_types=1);

namespace NGSOFT;

use ErrorException,
    InvalidArgumentException,
    JsonSerializable;
use NGSOFT\{
    Exceptions\RegExpException, Traits\PropertyAble
};
use Stringable,
    Traversable,
    TypeError;
use function get_debug_type,
             mb_str_split,
             mb_strlen,
             mb_substr,
             str_starts_with;

/**
 * Javascript Like Implementation for PHP
 * Sticky is too buggy (not implemented)
 *
 * 
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp
 * @link https://www.php.net/manual/en/book.pcre.php
 *
 * @property-read string $source The source property returns a String containing the source text of the regexp object, and it doesn't contain the two forward slashes on both sides and any flags.
 * @property-read string $flags The flags property returns a string consisting of the flags of the current regular expression object.
 * @property-read bool $dotAll The dotAll property indicates whether or not the "s" flag is used with the regular expression
 * @property-read bool $global The global property indicates whether or not the "g" flag is used with the regular expression.
 * @property-read bool $ignoreCase The ignoreCase property indicates whether or not the "i" flag is used with the regular expression.
 * @property-read bool $multiline  The multiline property indicates whether or not the "m" flag is used with the regular expression.
 *
 * @property int $lastIndex The lastIndex is a read/write integer property of regular expression instances that specifies the index at which to start the next match(only works with g flag).
 */
final class RegExp implements Stringable, JsonSerializable {

    use PropertyAble {
        __clone as __traitClone;
    }

    /**
     * Most used php delimiters
     */
    const PCRE_DELIMITERS = '%#/';

    /**
     * Case-insensitive search.
     */
    const PCRE_CASELESS = 'i';

    /**
     * Multi-line search.
     */
    const PCRE_MULTILINE = 'm';

    /**
     * Allows . to match newline characters.
     */
    const PCRE_DOTALL = 's';

    /**
     * If this modifier is set, whitespace data characters in the pattern are totally ignored except when escaped or inside a character class,
     * and characters between an unescaped # outside a character class and the next newline character, inclusive, are also ignored.
     */
    const PCRE_EXTENDED = 'x';

    /**
     * If this modifier is set, the pattern is forced to be "anchored", that is,
     * it is constrained to match only at the start of the string which is being searched (the "subject string").
     */
    const PCRE_ANCHORED = 'A';

    /**
     * If this modifier is set, a dollar metacharacter in the pattern matches only at the end of the subject string.
     * Without this modifier, a dollar also matches immediately before the final character if it is a newline (but not before any other newlines).
     * This modifier is ignored if m modifier is set.
     */
    const PCRE_DOLLAR_ENDONLY = 'D';

    /**
     * This modifier inverts the "greediness" of the quantifiers so that they are not greedy by default,
     * but become greedy if followed by ?.
     */
    const PCRE_UNGREEDY = 'u';

    /**
     * Allow duplicate names for subpatterns.
     */
    const PCRE_INFO_JCHANGED = 'j';

    /**
     * Global search.
     */
    const PCRE_GLOBAL = 'g';

    /**
     * Accepted Flags
     * @var string[]
     */
    const ACCEPTED_FLAGS = [
        self::PCRE_CASELESS,
        self::PCRE_MULTILINE,
        self::PCRE_DOTALL,
        self::PCRE_EXTENDED,
        self::PCRE_ANCHORED,
        self::PCRE_DOLLAR_ENDONLY,
        self::PCRE_UNGREEDY,
        self::PCRE_INFO_JCHANGED,
    ];

    /** @var string */
    private $pattern;

    /** @var string[] */
    private $modifiers = [];

    /** @var bool */
    private $isGlobal = false;

    /** @var int */
    private $index = 0;

    /** @var bool */
    private $regexTested = false;

    ///////////////////////////////// Implementation  /////////////////////////////////

    /**
     * Creates a new RegExp Object with the given argument
     * Marked as deprecated in MDN so changed that to new object creation within the object itself
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp/compile
     *
     * @param string $pattern full pattern or pattern without delimitters and modifiers
     * @param string|string[] $flags modifiers
     * @return static Other instance of RegExp
     */
    public function compile(string $pattern, string|array $flags = ''): self {
        return new static($pattern, $flags);
    }

    /**
     * The exec() method executes a search for a match in a specified string. Returns a result array, or null.
     * Will only gives the first result. if the global flag is set, the lastIndex from the previous match will be stored,
     * so you can loop through results (while loop).
     *
     * @param string $str  The string against which to match the regular expression
     * @return array|null If the match fails, the exec() method returns null, and sets lastIndex to 0.
     */
    public function exec(string $str): ?array {
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
     * The test() method executes a search for a match between a regular expression and a specified string.
     * @param string $str
     * @return bool
     */
    public function test(string $str): bool {
        $result = $this->exec($str);
        return $result !== null;
    }

    /**
     * The replace() method replaces some or all matches of a this pattern in a string by a replacement,
     * and returns the result of the replacement as a new string.
     * If global modifier is used all occurences will be replaced, else only the first occurence will be replaced.
     *
     * @suppress PhanRedundantCondition
     * @param string $str
     * @param string|callable $replacement
     * @return string
     * @throws TypeError
     */
    public function replace(string $str, $replacement): string {
        $this->assertValidRegex();
        if (
                !is_callable($replacement) and
                !is_string($replacement)
        ) {
            throw new TypeError(sprintf('Argument 2 passed to replace() must be of the type callable|string, %s given.', get_debug_type($replacement)));
        }

        $method = !is_string($replacement) ? 'preg_replace_callback' : 'preg_replace';
        $arguments = [
            $this->getUsableRegex(),
            $replacement,
            $str,
            $this->isGlobal ? -1 : 1
        ];
        return $this->execute($method, $arguments, null);
    }

    /**
     * The search() method executes a search for a match between a regular expression and a string.
     *
     * @param string $str
     * @return int The index of the first match between the regular expression and the given string, or -1 if no match was found.
     */
    public function search(string $str): int {
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
    public function split(string $str, int $limit = -1): array {
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
    public function matchAll(string $str): Traversable {
        $this->assertValidRegex();
        if (!$this->isGlobal) {
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

            for ($i = 0; $i < count($matches); $i++) yield $i => $matches[$i];
        }
    }

    /**
     * The matchOne() method retrieves the result of matching a string against a regular expression.
     * @param string $str
     * @return array|null
     */
    public function matchOne(string $str): ?array {
        $this->assertValidRegex();
        if (
                !$this->isGlobal
        ) return $this->exec($str);

        $this->setLastIndex(0);

        $matches = [];
        $arguments = [
            $this->getUsableRegex(),
            $str,
            &$matches,
            PREG_SET_ORDER
        ];

        if ($this->execute('preg_match_all', $arguments, false) > 0) return array_map(fn($array) => $array[0], $matches);
        return null;
    }

    ///////////////////////////////// Initialisation  /////////////////////////////////

    /**
     * Initialize RegExp
     * @param string $pattern full pattern or pattern without delimitters and modifiers
     * @param string $flags modifiers
     * @return static
     */
    public static function create(string $pattern, string $flags = ''): self {
        return new static($pattern, $flags);
    }

    /**
     * Initialize RegExp
     * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp Same as that except pattern is a string
     * @param string $pattern full pattern or pattern without delimitters and modifiers
     * @param string|string[] $flags modifiers
     */
    public function __construct(
            string $pattern,
            string|array $flags = ''
    ) {
        $this->silentProperties = false;
        $this->lazyProperties = false;
        if (!empty($pattern)) $this->initialize($pattern, $flags);
    }

    /**
     * Handles __construct and unserialize
     * @param string $pattern
     * @param string|string[] $flags
     * @throws InvalidArgumentException
     */
    private function initialize(
            string $pattern,
            string|array $flags
    ) {

        if (is_string($flags)) $flags = mb_str_split($flags);


        foreach (str_split(self::PCRE_DELIMITERS) as $delimiter) {
            if (str_starts_with($pattern, $delimiter)) {

                if (preg_match(sprintf('/[\%s]([a-z]*)$/i', $delimiter), $pattern, $matches) > 0) {
                    list($suffix, $addedFlags) = $matches;
                    $pattern = mb_substr($pattern, 1);
                    $pattern = mb_substr($pattern, 0, mb_strlen($pattern) - mb_strlen($suffix));
                    if (!empty($addedFlags)) $flags = array_merge($flags, mb_str_split($addedFlags));
                    break;
                }
                throw new InvalidArgumentException(sprintf('Invalid Pattern "%s" delimiters.', $pattern));
            }
        }
        $this->pattern = $pattern;
        foreach ($flags as $flag) {
            if ($flag == self::PCRE_GLOBAL) {
                $this->isGlobal = true;
                continue;
            }

            if (!in_array($flag, self::ACCEPTED_FLAGS)) {
                throw new InvalidArgumentException(sprintf('Invalid flag detected "%s" (accepted: %s).', $flag, implode('', array_merge(self::ACCEPTED_FLAGS, [self::PCRE_GLOBAL]))));
            }
            if (!in_array($flag, $this->modifiers)) $this->modifiers[] = $flag;
        }

        //check if valid regex
        $this->regexTested = false;
        $this->assertValidRegex();
        //add properties
        $this
                ->setProperty('source', $this->pattern, false, false)
                ->setProperty(
                        'flags',
                        implode('', $this->modifiers) . ($this->isGlobal ? self::PCRE_GLOBAL : ''),
                        false,
                        false
                )
                ->setProperty('dotAll', in_array(self::PCRE_DOTALL, $this->modifiers), false, false)
                ->setProperty('global', in_array(self::PCRE_GLOBAL, $this->modifiers), false, false)
                ->setProperty('ignoreCase', in_array(self::PCRE_CASELESS, $this->modifiers), false, false)
                ->setProperty('multiline', in_array(self::PCRE_MULTILINE, $this->modifiers), false, false)
                ->setProperty('lastIndex', [
                    'get' => [$this, 'getLastIndex'],
                    'set' => [$this, 'setLastIndex'],
                        ], false, false);
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
    private function execute(callable $callback, array $arguments, $expectedErrorReturnValue) {

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
     */
    private function assertValidRegex() {
        if ($this->regexTested) return;
        Tools::safe_exec(function () {
            $regex = $this->getUsableRegex();
            $method = $this->isGlobal ? 'preg_match_all' : 'preg_match';

            if (false === $method($regex, '')) {
                throw new RegExpException($this, sprintf('Invalid regex "%s".', $this));
            }
            $this->regexTested = true;
        });
    }

    /**
     * Get the regex string accepted by pcre functions
     * @return string
     */
    private function getUsableRegex(): string {
        return sprintf('#%s#%s', $this->pattern, implode('', $this->modifiers));
    }

    ///////////////////////////////// Getters/Setters  /////////////////////////////////

    /**
     * Get the last index
     * @return int
     */
    public function getLastIndex(): int {
        return $this->index;
    }

    /**
     * Set the Last Index
     * @param int $index
     * @return static
     */
    public function setLastIndex(int $index): self {
        $this->index = $index;
        return $this;
    }

    ///////////////////////////////// Import/Export  /////////////////////////////////

    /** {@inheritdoc} */
    public function __clone() {
        $this->__traitClone();
        $this->regexTested = false;
    }

    /**
     * {@inheritdoc}
     * @staticvar string $regex
     * @return string
     */
    public function __toString() {
        $regex = $this->getUsableRegex();
        if ($this->isGlobal) $regex .= self::PCRE_GLOBAL;
        return $regex;
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed {
        return $this->__toString();
    }

    /** {@inheritdoc} */
    public function __serialize() {

        return [
            'pattern' => $this->pattern,
            'flags' => $this->modifiers,
        ];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data) {
        $pattern = $data['pattern'];
        $flags = $data['flags'];
        $this->__construct($pattern, $flags);
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        return [
            'pattern' => $this->__toString()
        ];
    }

}
