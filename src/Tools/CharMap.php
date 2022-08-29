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
class CharMap
{

    protected Map $map;
    protected int $length;
    protected int $size;

    public function __construct(protected string $string)
    {
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


        for ($offset = 0; $offset < $this->length; $offset ++) {

        }



        $offsets = &$this->offsets;

        for ($i = 0; $i < $this->length; $i ++) {
            $char = mb_substr($this->text, $i, 1);
            for ($j = 0; $j < strlen($char); $j ++) {
                $offsets[0][] = $i;
                $offsets[1][$i] ??= array_key_last($offsets[0]);
            }
        }
    }

    /**
     * Get Character Offset from Byte Offset
     */
    public function getCharOffset(int $byteOffset): int
    {
        if ( ! in_range($byteOffset, 0, $this->size - 1)) {
            throw new OutOfRangeException(sprintf('Byte offset %d is invalid [ 0-%d ].', $byteOffset, $this->size - 1));
        }

        if ($this->size === $this->length || $byteOffset === 0) {
            return $byteOffset;
        }
    }

    /**
     * Get Byte offset from Character Offset
     */
    public function getByteOffset(int $charOffset): int
    {

        if ( ! in_range($charOffset, 0, $this->length - 1)) {
            throw new OutOfRangeException(sprintf('Character offset %d is invalid [ 0-%d ].', $charOffset, $this->length - 1));
        }

        if ($this->size === $this->length) {
            return $charOffset;
        }
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

}
