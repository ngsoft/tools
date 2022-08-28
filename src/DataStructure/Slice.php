<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use InvalidArgumentException;
use NGSOFT\{
    Tools, Tools\TypeCheck
};
use Stringable;
use function class_basename,
             str_contains;

class Slice implements Stringable
{

    public static function create(?int $start = null, ?int $stop = null, ?int $step = null): static
    {
        return new static($start, $stop, $step);
    }

    public static function of(string|int $slice): static
    {

        $stop = null;
        $step = 1;

        if ($slice === ':' || $slice === '::') {
            $start = 0;
        } elseif (str_contains($slice, ':')) {

            @list($start, $stop, $step) = explode(':', $slice);

            if ( ! is_numeric($step)) {
                $step = 1;
            }

            $step = intval($step);

            if ( ! is_numeric($start)) {
                $start = null;
            } else { $start = intval($start); }

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

    /**
     * Returns a slice of an array like object
     */
    public function slice(mixed $value): array
    {

        TypeCheck::assertType(
                __METHOD__ . ' Argument #0', $value,
                TypeCheck::TYPE_ARRAYACCESS
        );

        [$start, $stop, $step, $len, $result] = [$this->start, $this->stop, $this->step, count($value), []];
        if ($len === 0) {
            return $result;
        }

        /**
         * @link https://www.bestprog.net/en/2019/12/07/python-strings-access-by-indexes-slices-get-a-fragment-of-a-string-examples/
         */
        $step ??= 1;

        if ($step > 0) {

            if (is_null($start) && is_null($stop)) {
                $start = 0;
                $stop = $len;
            } elseif (is_null($stop)) {
                $stop = $len;
            } elseif (is_null($start)) {
                $start = 0;
            }
        } else {
            if (is_null($start) && is_null($stop)) {
                $start = $len - 1;
                $stop = -1;
            } elseif (is_null($stop)) {
                $stop = -1;
            } elseif (is_null($start)) {
                $start = $len - 1;
            }
        }

        while ($start < 0) {
            $start += $len;
        }

        while ($stop < ($step < 0 ? -1 : 0)) {
            $stop += $len;
        }


        $start = max(0, min($start, $len - 1));
        $stop = max(-1, min($stop, $len));

        foreach (Range::create($start, $stop, $step) as $offset) {


            if ($offset >= $len && $step > 0) {
                break;
            }

            if ($offset < 0 && $step < 0) {
                break;
            }

            if (is_null($value[$offset] ?? null)) {
                continue;
            }

            $result[] = $value[$offset];
        }

        return $result;
    }

    /**
     * Returns a String of a slice
     */
    public function join(mixed $glue, mixed $value): string
    {
        return Tools::join($glue, $this->slice($value));
    }

    public function __toString(): string
    {
        return sprintf(
                '%s(%s,%s,%s)',
                class_basename($this),
                is_null($this->start) ? 'null' : (string) $this->start,
                is_null($this->stop) ? 'null' : (string) $this->stop,
                is_null($this->step) ? 'null' : (string) $this->step
        );
    }

}
