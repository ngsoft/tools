<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    NGSOFT\DataStructure\Map,
    OutOfRangeException,
    RuntimeException,
    Stringable;
use function in_range,
             mb_strlen,
             mb_substr;

/**
 * A Multibyte/byte string convertion Map
 */
class CharMap implements Stringable, Countable
{

    protected static array $_instances = [];
    protected string $string;
    protected int $size;
    protected int $length;
    protected Map $map;

    ////////////////////////////   Static Methods   ////////////////////////////

    /**
     * Create a new CharMap
     */
    public static function create(string $string): static
    {

        static $empty;

        if ($string !== '')
        {
            return self::$_instances[$string] ??= new static($string);
        }

        return $empty ??= new static('');
    }

    /**
     * Get character offset from byte offset
     * Returns -1 on failure
     */
    public static function getCharOffset(string $string, int $byte): int
    {
        try
        {

            return static::create($string)->convertByteOffset($byte);
        }
        catch (OutOfRangeException | RuntimeException)
        {
            return -1;
        }
    }

    /**
     * Get byte offset from character Offset
     * returns -1 on failure
     */
    public static function getByteOffset(string $string, int $char): int
    {
        try
        {
            return static::create($string)->convertCharacterOffset($char);
        }
        catch (OutOfRangeException | RuntimeException)
        {
            return -1;
        }
    }

    ////////////////////////////   Implementation   ////////////////////////////

    /**
     * Create a new CharMap
     */
    public function __construct(string $string)
    {
        $this->string = $string;
        $this->length = mb_strlen($string);
        $this->size = strlen($string);
        $this->map = new Map();
    }

    protected function getMap(): Map
    {

        $this->scan();
        return $this->map;
    }

    protected function scan(): void
    {

        if ( ! $this->map->isLocked())
        {
            return;
        }


        $index = 0;
        for ($offset = 0; $offset < $this->length; $offset ++)
        {
            $char = mb_substr($this->string, $offset, 1);
            for ($byte = 0; $byte < strlen($char); $byte ++)
            {
                $this->map->add($index, $offset);
                $index ++;
            }
        }

        $this->map->lock();
    }

    /**
     * Get Character Offset from Byte Offset
     */
    public function convertByteOffset(int $byte): int
    {

        if (0 === $byte)
        {
            return $byte;
        }


        if ($this->isEmpty() || ! in_range($byte, 0, $this->size - 1))
        {
            throw new OutOfRangeException(sprintf('Byte offset %d is invalid [ 0-%d ].', $byte, $this->size - 1));
        }

        if ($this->size === $this->length)
        {
            return $byte;
        }

        if (null === $offset = $this->getMap()->get($byte))
        {

            throw new RuntimeException(sprintf('Cannot find offset for byte #%d.', $byte));
        }

        return $offset;
    }

    /**
     * Get Byte offset from Character Offset
     */
    public function convertCharacterOffset(int $char): int
    {
        if (0 === $char)
        {
            return $char;
        }

        if ($this->isEmpty() || ! in_range($char, 0, $this->length - 1))
        {
            throw new OutOfRangeException(sprintf('Character offset %d is invalid [ 0-%d ].', $char, $this->length - 1));
        }

        if ($this->size === $this->length)
        {
            return $char;
        }

        if (null === $offset = $this->getMap()->search($char))
        {
            throw new RuntimeException(sprintf('Cannot find offset for character #%d.', $char));
        }


        return $offset;
    }

    /**
     * Get number of characters
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Get number of bytes
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function count(): int
    {
        return $this->length;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function __unserialize(array $data)
    {
        @list($this->string, $this->length, $this->size, $this->map) = $data;
    }

    public function __serialize(): array
    {
        return [$this->string, $this->length, $this->size, $this->map];
    }

}
