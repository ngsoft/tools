<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

class Tuple extends \SplFixedArray
{

    protected array $prototype;

    public function __construct(array $prototype, array $data = []): \SplFixedArray
    {

        $prototype = array_values($prototype);
        $data = array_values($data);

        parent::__construct(count($prototype));

        $offset = 0;

        foreach ($data as $offset => $value) {
            $this->offsetSet($offset, $value);
            $offset ++;
        }
    }

    protected function isValid(int $offset, mixed $value): bool
    {

        if ( ! isset($this->prototype[$offset])) {
            return false;
        }

        $type = $this->prototype[$offset];

        if (class_exists($type) || interface_exists($type)) {
            return is_object($value) && is_a($value, $type);
        }

        return $type === 'mixed' || get_debug_type($value) === $type;
    }

    public function offsetSet(int $index, mixed $value): void
    {

        if ( ! $this->isValid($index, $value)) {
            throw new RuntimeException(sprintf('Invalid offset %d or type %s for value.', $index, get_debug_type($value)));
        }


        parent::offsetSet($index, $value);
    }

    public function __toString(): string
    {
        return sprintf('%s#%d(%s)', static::class, spl_object_id($this), implode(', ', $this->prototype));
    }

}
