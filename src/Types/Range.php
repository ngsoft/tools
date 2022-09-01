<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use Countable;

class Range extends pSequence
{

    public readonly int $start;
    public readonly int $stop;
    public readonly int $step;
    protected ?int $count = null;

    /**
     * Creates a Range
     */
    public static function create(int $start, ?int $stop = null, int $step = 1): static
    {
        return new static($start, $stop, $step);
    }

    /**
     * Get a range for a Countable
     */
    public static function of(Countable|array $countable): static
    {
        return new static(0, count($countable));
    }

    public function __construct(
            int $start,
            ?int $stop = null,
            int $step = 1
    )
    {
        if ($step === 0) {
            throw new ValueError('Step cannot be 0');
        }

        if (is_null($stop)) {
            $stop = $start;
            $start = 0;
        }

        [$this->start, $this->stop, $this->step] = [$start, $stop, $step];
    }

    public function isEmpty(): bool
    {
        return $this->step > 0 ? $this->stop <= $this->start : $this->stop >= $this->start;
    }

    protected function __len__(): int
    {

        if (is_null($this->count)) {

            if ($this->isEmpty()) {
                return $this->count = 0;
            }
            [$min, $max, $step] = [$this->start, $this->stop, abs($this->step)];

            if ($min > $max) {
                [$min, $max] = [$max, $min];
            }

            return $this->count = intval(ceil(($max - $min) / $step));
        }

        return $this->count;
    }

    protected function __getitem__(int $offset): mixed
    {
        return $this->start + ($offset * $this->step);
    }

    public function __repr__(): string
    {
        return sprintf('%s::create(%d, %d, %d)', static::class, $this->start, $this->stop, $this->step);
    }

}
