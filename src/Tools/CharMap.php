<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use NGSOFT\DataStructure\Map;

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
