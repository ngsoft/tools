<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use NGSOFT\{
    Tools, Types\Traits\IsReversible
};
use Throwable,
    Traversable;
use function in_range;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
abstract class pSequence implements pReversible, pCollection
{

    use IsReversible;

    protected array $data = [];

    /** {@inheritdoc} */
    public function contains(mixed $value): bool
    {

        foreach ($this as $_value) {

            if ($value === $_value) {
                return true;
            }
        }

        return false;
    }

    protected function getValue(mixed $value): mixed
    {

        if ($value instanceof self) {
            return $value->data;
        }

        return $value;
    }

    /**
     * Return first offset of value.
     * Raises ValueError if the value is not present.
     *
     */
    public function index(mixed $value, int $start = 0, ?int $stop = null): int
    {

        if ($start < 0) {
            $start = max($this->count() + $start, 0);
        }

        if ($stop < 0) {
            $stop += $this->count();
        }


        $offset = $start;

        $value = $this->getValue($value);

        while (is_null($stop) || $offset < $stop) {

            try {

                $_value = $this->getValue($this[$offset]);

                if ($_value === $value) {
                    return $offset;
                }
            } catch (Throwable) {
                break;
            }
            $offset ++;
        }

        throw ValueError::for($value, $this);
    }

    public function count(mixed $value = null): int
    {
        return count($this->data);
    }

    /**
     * return number of occurrences of value
     */
    public function countValue(mixed $value): int
    {
        return Tools::countValue($value, $this);
    }

    protected function getOffset(Slice|int|string|null $offset): Slice|int
    {

        if (is_null($offset)) {
            return $this->count();
        }
        if (is_string($offset) && ! Slice::isValid($offset)) {
            throw IndexError::for($offset, $this);
        }


        if (is_int($offset) && $offset < 0) {
            $offset += $this->count();

            if ($offset === -1 && ! $this->count()) {
                $offset = 0;
            }
        } elseif (is_string($offset)) {
            $offset = Slice::of($offset);
        }

        return $offset;
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ( ! $this->count()) {
            throw IndexError::for($offset, $this);
        }


        $offset = $this->getOffset($offset);

        if (is_int($offset)) {
            if ( ! in_range($offset, 0, $this->count() - 1)) {
                throw IndexError::for($offset, $this);
            }

            return $this->data[$offset];
        }

        return $this->withData($offset->slice($this));
    }

    public function offsetExists(mixed $offset): bool
    {

        try {
            return $this[$offset] !== null;
        } catch (Throwable) {
            return false;
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // nothing to do
    }

    public function offsetUnset(mixed $offset): void
    {
        // nothing to do
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        $offset = 0;

        try {
            while ($this->offsetExists($offset)) {
                $value = $this[$offset];
                yield $value;
                $offset ++;
            }
        } catch (Throwable) {
            return;
        }
    }

    /** {@inheritdoc} */
    public function getReverseIterator(): Traversable
    {

        foreach (Range::of($this)->getReverseIterator() as $offset) {
            yield $this[$offset];
        }
    }

    public function __serialize(): array
    {
        return [$this->data];
    }

    public function __unserialize(array $data)
    {
        [$this->data] = $data;
    }

    public function __debugInfo(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    public function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

}
