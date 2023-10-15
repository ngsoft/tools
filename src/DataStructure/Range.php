<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable;
use NGSOFT\Traits\ReversibleIteratorTrait;

/**
 * Returns a sequence of numbers, starting from 0 by default, and increments by 1 (by default), and stops before a specified number.
 */
final class Range implements ReversibleIterator, \Stringable
{
    use ReversibleIteratorTrait;

    public readonly int $start;
    public readonly ?int $stop;
    public readonly int $step;
    private ?array $values = null;
    private ?int $length   = null;

    public function __construct(
        int $start,
        ?int $stop = null,
        int $step = 1
    ) {
        if (0 === $step)
        {
            throw new \ValueError('Step cannot be 0');
        }

        if (is_null($stop))
        {
            $stop  = $start;
            $start = 0;
        }

        [$this->start, $this->stop, $this->step] = [$start, $stop, $step];
    }

    // //////////////////////////   Implementation   ////////////////////////////

    public function __debugInfo(): array
    {
        return iterator_to_array($this->getIterator());
    }

    public function __toString(): string
    {
        return sprintf('%s::create(%d, %d, %d)', self::class, $this->start, $this->stop, $this->step);
    }

    // //////////////////////////   Static methods   ////////////////////////////

    /**
     * Creates a Range.
     */
    public static function create(int $start, ?int $stop = null, int $step = 1): static
    {
        return new self($start, $stop, $step);
    }

    /**
     * Get a range for a Countable.
     */
    public static function of(\Countable|array $countable): static
    {
        return new static(0, count($countable));
    }

    /**
     * Checks if empty range.
     */
    public function isEmpty(): bool
    {
        return $this->step > 0 ? $this->stop <= $this->start : $this->stop >= $this->start;
    }

    public function count(): int
    {
        if (is_null($this->length))
        {
            $this->length = 0;

            if ( ! $this->isEmpty())
            {
                [$min, $max, $step] = [$this->start, $this->stop, abs($this->step)];

                if ($min > $max)
                {
                    [$min, $max] = [$max, $min];
                }

                $this->length       = intval(ceil(($max - $min) / $step));
            }
        }

        return $this->length;
    }

    public function entries(Sort $sort = Sort::ASC): iterable
    {
        if ( ! $this->isEmpty())
        {
            if (Sort::DESC === $sort)
            {
                for ($offset = -1; $offset >= -$this->count(); --$offset)
                {
                    yield $this->getOffset($offset);
                }
            } else
            {
                for ($offset = 0; $offset < $this->count(); ++$offset )
                {
                    yield $this->getOffset($offset);
                }
            }
        }
    }

    private function getOffset(int $offset): int
    {
        if (0 > $offset)
        {
            $offset += $this->count();
        }
        return $this->start + ($offset * $this->step);
    }
}
