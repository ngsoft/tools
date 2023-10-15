<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

trait Exportable
{
    public function __debugInfo(): array
    {
        return $this->export();
    }

    public function __toString(): string
    {
        return sprintf('object(%s)', static::class);
    }

    public function __unserialize(array $data): void
    {
        $this->import($data);
    }

    public function __serialize(): array
    {
        return $this->export();
    }

    public function jsonSerialize(): mixed
    {
        return $this->export();
    }

    /**
     * Imports data from an array.
     *
     * @param array $array data to import
     */
    abstract protected function import(array $array): void;

    /**
     * Exports datas from the object to array representation.
     */
    abstract protected function export(): array;

    /**
     * Traverses an array and clone its values
     * can be used using __clone() magic method.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function recursiveCloner($value)
    {
        if (is_object($value))
        {
            if ($value === $this)
            {
                return $value;
            }
            return clone $value; // thats why this method exists
        }

        if (is_array($value)) // need to traverse to find objects
        {
            $newvalue = [];

            foreach ($value as $key => $val)
            {
                $newvalue[$key] = $this->recursiveCloner($val);
            }
            return $newvalue;
        }  return $value;
    }

    /**
     * Create array containing named properties and their values
     * same as builtin compact but with properties.
     *
     * @param string ...$properties List of property names.
     *
     * @throws \LogicException if a property does not exists
     */
    protected function compact(string ...$properties): array
    {
        $result = [];

        foreach ($properties as $prop)
        {
            if ( ! property_exists($this, $prop))
            {
                throw new \LogicException(sprintf('Cannot compact %s::$%s as it does not exists.', static::class, $prop));
            }
            $result[$prop] = $this->{$prop};
        }

        return $result;
    }

    /**
     * Imports properties into the current instance
     * same as php builtin but for properties.
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function extract(array $array): void
    {
        foreach ($array as $prop => $value)
        {
            if ( ! is_string($prop))
            {
                throw new \InvalidArgumentException(sprintf('Invalid property type: string requested %s given.', \get_debug_type($prop)));
            }

            if ( ! property_exists($this, $prop))
            {
                throw new \UnexpectedValueException(sprintf('Property %s::$%s cannot be extracted as it does not exists.', static::class, $prop));
            }
            $this->{$prop} = $value;
        }
    }
}
