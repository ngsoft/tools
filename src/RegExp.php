<?php

declare(strict_types=1);

namespace NGSOFT;

use InvalidArgumentException;
use NGSOFT\{
    Attributes\HasProperties, Exceptions\RegExpException, Tools\PropertyAble
};
use Stringable;
use function mb_str_split,
             mb_strlen,
             mb_substr,
             str_starts_with;

/**
 * @property int $lastIndex
 */
#[HasProperties(lazy: false)]
class RegExp extends PropertyAble implements Stringable {

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

    public readonly string $source;
    public readonly string $flags;
    public readonly bool $dotAll;
    public readonly bool $global;
    public readonly bool $ignoreCase;
    public readonly bool $multiline;

    /** @var string[] */
    private array $modifiers = [];

    /** @var bool */
    private bool $isGlobal = false;

    /** @var int */
    private int $index = 0;

    /** @var bool */
    private $tested = false;

    public function __construct(
            string $pattern,
            string|array $flags = ''
    ) {

        if (is_string($flags)) $flags = mb_str_split($flags);
        $cleanPattern = $pattern;

        foreach (str_split(self::PCRE_DELIMITERS) as $delimiter) {
            if (str_starts_with($pattern, $delimiter)) {
                if (preg_match(sprintf('/[\%s]([a-z]*)$/i', $delimiter), $pattern, $matches) > 0) {
                    list($suffix, $addedFlags) = $matches;
                    $cleanPattern = mb_substr($pattern, 1);
                    $cleanPattern = mb_substr($cleanPattern, 0, mb_strlen($cleanPattern) - mb_strlen($suffix));

                    if (!empty($addedFlags)) $flags = array_merge($flags, mb_str_split($addedFlags));
                    break;
                }
                throw new InvalidArgumentException(sprintf('Invalid Pattern "%s" delimiters.', $pattern));
            }
        }

        foreach ($flags as $flag) {
            if ($flag === self::PCRE_GLOBAL) {
                $this->isGlobal = true;
                continue;
            }
            if (!in_array($flag, self::ACCEPTED_FLAGS)) {
                throw new InvalidArgumentException(sprintf('Invalid flag "%s" (accepted: %s).', $flag, implode('', array_merge(self::ACCEPTED_FLAGS, [self::PCRE_GLOBAL]))));
            }
            $this->modifiers[$flag] = $flag;
        }

        $this->tested = false;

        var_dump($cleanPattern, $flags);
    }

    /**
     * Test if regex is valid
     *
     */
    private function assertValidRegex() {
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
    private function getUsableRegex(): string {
        return sprintf('#%s#%s', $this->pattern, implode('', $this->modifiers));
    }

    public function __toString(): string {
        $regex = $this->getUsableRegex();
        if ($this->isGlobal) $regex .= self::PCRE_GLOBAL;
        return $regex;
    }

}
