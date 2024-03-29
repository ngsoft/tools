<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

/**
 * Transforms any class that extends it as a tuple.
 *
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
abstract class Tuple implements \ArrayAccess
{
    public function offsetExists(mixed $offset): bool
    {
        return null !== $this->offsetGet($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $data = $this->getTuple();

        if (is_int($offset))
        {
            $offsets = array_keys($data);
            $offset  = $offsets[$offset] ?? null;
        }

        if ( ! is_string($offset))
        {
            return null;
        }

        return $data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Offsets cannot be set on a ' . \class_basename(__CLASS__) . ' except if you implement ' . __FUNCTION__);
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Offsets cannot be unset on a ' . \class_basename(__CLASS__) . ' except if you implement ' . __FUNCTION__);
    }

    /**
     * Override this function to select named values to expand as a list.
     *
     * eg: ['property1'=>$var1, 'property2'=>$var2]=$tuple;
     * or: [$var1,$var2]=$tuple;
     *
     * @return array<string, mixed>
     */
    protected function getTuple(): array
    {
        // works with protected|public properties (not private)
        return get_object_vars($this);
    }
}
