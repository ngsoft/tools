<?php

declare(strict_types=1);

namespace NGSOFT;

use Countable,
    IteratorAggregate;
use NGSOFT\Traits\{
    CloneUtils, ReversibleIteratorTrait
};
use Stringable;
use function mb_strlen;

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
                for ($offset = 0; $offset < $this->length; $offset ++)
                {
                    $char = mb_substr($this->text, $offset, 1, $this->encoding);
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

    public function getIterator(): \Traversable
    {

        if ($this->length > 0)
        {
            for ($offset = 0; $offset < $this->length; $offset ++)
            {

                yield mb_substr($this->text, $offset, 1, $this->encoding);
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

    ////////////////////////////   JS Implementation (with some modifications)   ////////////////////////////




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

}
