<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use NGSOFT\DataStructure\Map,
    OutOfRangeException;
use function in_range,
             mb_strlen;

/**
 * A Multibyte/byte string convertion Map
 */
class CharMap extends \NGSOFT\DataStructure\Tuple implements \Stringable, \Countable
{

    protected string $string;
    protected int $size;
    protected int $length;
    protected Map $map;

    /**
     * This is the order fo the indexes of the Tuple
     * [$length, $size, string] = $charmap
     */
    protected function getTuple(): array
    {
        return [
            'length' => $this->length,
            'size' => $this->size,
            'string' => $this->string,
        ];
    }

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

        if ( ! $this->map->isEmpty()) {
            return;
        }


        $index = 0;
        for ($offset = 0; $offset < $this->length; $offset ++ ) {
            $char = mb_substr($this->string, $offset, 1);
            for ($byte = 0; $byte < strlen($char); $byte ++ ) {
                $this->map->add($index, $offset);
                $index ++;
            }
        }

        $this->map->lock();
    }

    /**
     * Get Character Offset from Byte Offset
     */
    public function getCharOffset(int $byteOffset): int
    {

        if (0 === $byteOffset) {
            return $byteOffset;
        }

        if ($this->isEmpty() || ! in_range($byteOffset, 0, $this->size - 1)) {
            throw new OutOfRangeException(sprintf('Byte offset %d is invalid [ 0-%d ].', $byteOffset, $this->size - 1));
        }

        if ($this->size === $this->length) {
            return $byteOffset;
        }


        return mb_strlen(substr($this->string, 0, $byteOffset));

        return $this->getMap()->get($byteOffset);
    }

    /**
     * Get Byte offset from Character Offset
     */
    public function getByteOffset(int $charOffset): int
    {
        if (0 === $charOffset) {
            return $charOffset;
        }

        if ($this->isEmpty() || ! in_range($charOffset, 0, $this->length - 1)) {
            throw new OutOfRangeException(sprintf('Character offset %d is invalid [ 0-%d ].', $charOffset, $this->length - 1));
        }

        if ($this->size === $this->length) {
            return $charOffset;
        }


        return strlen(mb_substr($this->string, 0, $charOffset));

        return $this->getMap()->search($charOffset);
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

    public function toString(): string
    {
        return $this->string;
    }

    public function __toString(): string
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
