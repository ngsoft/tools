<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use Throwable;

class iList extends MutableSequence
{

    protected array $data = [];

    public function __construct(
            iterable $list = []
    )
    {
        $this->extend($list);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function copy(): static
    {
        return new self($this);
    }

    protected function getOffset(Slice|int|string|null $offset): array|int
    {

        if (is_null($offset)) {
            return $this->count();
        }

        if (is_int($offset)) {

            if ($offset < 0) {
                return $offset + $this->count() + 1;
            }

            return $offset;
        }

        if (is_string($offset)) {
            if ( ! Slice::isValid($offset)) {
                throw IndexError::for($offset, $this);
            }

            $offset = Slice::of($offset);
        }

        return $offset->getOffsetList($this);
    }

    /**
     * Helper to be used with __clone() method
     */
    protected function cloneArray(array $array): array
    {

        foreach ($array as $offset => $value) {

            if (is_object($value)) {
                $array[$offset] = clone $value;
            }


            if (is_array($value)) {
                $array[$offset] = $this->cloneArray($value);
            }
        }

        return $array;
    }

    public function __clone()
    {
        $this->list = $this->cloneArray($this->list);
    }

}
