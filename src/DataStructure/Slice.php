<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    InvalidArgumentException,
    NGSOFT\Tools\TypeCheck;
use function str_contains;

class Slice
{

    public static function create(?int $start = null, ?int $stop = null, ?int $step = null): static
    {
        return new static($start, $stop, $step);
    }

    public static function of(string|int $slice): static
    {

        $start = $stop = null;
        $step = 1;

        if (is_numeric($slice)) {

            $start = intval($slice);
            $stop = $start + 1;
        } elseif ($slice === ':' || $slice === '::') {
            $start = 0;
        } elseif (str_contains($slice, ':')) {

            @list($start, $stop, $step) = explode(':', $slice);

            if ( ! is_numeric($step)) {
                $step = 1;
            }

            $step = intval($step);

            if ( ! is_numeric($start)) {
                $start = 0;
            }

            $start = intval($start);

            if ( ! is_numeric($stop)) {
                $stop = null;
            } else { $stop = intval($stop); }
        } else { throw new InvalidArgumentException(sprintf('Invalid slice "%s"', (string) $slice)); }

        return self::create($start, $stop, $step);
    }

    public function __construct(
            protected ?int $start = null,
            protected ?int $stop = null,
            protected ?int $step = null
    )
    {

    }

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function getStop(): ?int
    {
        return $this->stop;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    protected function getOffset(int $offset): int
    {
        return ($this->start ?? 0) + ($offset * ($this->step ?? 1));
    }

    public function slice(mixed $value): array
    {

        TypeCheck::assertType(
                __METHOD__ . ' Argument #0', $value,
                TypeCheck::TYPE_ARRAY, TypeCheck::UNION, ArrayAccess::class, TypeCheck::INTERSECTION, Countable::class
        );

        [$start, $stop, $step, $len, $result] = [$this->start, $this->stop, $this->step, count($value), []];

        if ($len === 0) {
            return $result;
        }

        $step ??= 1;
        $start ??= 0;
        $stop ??= $len;

        if ($step > 0 ? $stop <= $start : $stop >= $start) {
            return $result;
        }


        $range = new Range($start, $stop, $step);

        foreach ($range as $offset) {



            $unsigned ??= $offset >= 0;

            if ($unsigned !== $offset >= 0) {
                break;
            }

            while ($offset < 0) {
                $offset += $len;
            }

            if ($offset >= $len) {
                break;
            }

            if (is_null($value[$offset] ?? null)) {
                continue;
            }

            $result[] = $value[$offset];
        }


        return $result;
    }

}
