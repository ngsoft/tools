<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use InvalidArgumentException;
use NGSOFT\{
    Tools, Tools\TypeCheck
};
use Stringable,
    Throwable;
use function class_basename,
             str_contains;

class Slice implements Stringable
{

    private const RE_SLICE = '#^(|-?\d+)(?::(|-?\d+))(?::(|-?\d+))?$#';

    public static function create(?int $start = null, ?int $stop = null, ?int $step = null): static
    {
        return new static($start, $stop, $step);
    }

    /**
     * Create a Slice instance using python slice notation
     *
     * @link https://www.bestprog.net/en/2019/12/07/python-strings-access-by-indexes-slices-get-a-fragment-of-a-string-examples/
     * eg ':' '::' '0:1:' '10:2:-1' '1:'
     */
    public static function of(string $slice): static
    {


        if ( ! self::isValid($slice)) {
            throw new InvalidArgumentException(sprintf('Invalid slice [%s]', $slice));
        }

        if ($slice === ':' || $slice === '::') {
            return static::create(0);
        }

        @list(, $start, $stop, $step) = preg_exec(self::RE_SLICE, $slice);

        foreach ([&$start, &$stop, &$step] as &$value) {

            if (is_numeric($value)) {
                $value = intval($value);
            } else { $value = null; }
        }
        return self::create($start, $stop, $step);
    }

    public static function isValid(string $slice): bool
    {

        if ( ! str_contains($slice, ':')) {
            return false;
        }

        if ($slice === ':' || $slice === '::') {
            return true;
        }

        return preg_test(self::RE_SLICE, $slice);
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

        $step ??= 1;
        $stop ??= $step > 0 ? $len : -1;
        $start ??= $step > 0 ? 0 : $len - 1;

        while ($start < 0) {
            $start += $len;
        }

        while ($stop < ($step < 0 ? -1 : 0)) {
            $stop += $len;
        }


        foreach (Range::create($start, $stop, $step) as $offset) {


            if ($offset >= $len && $step > 0) {
                break;
            }

            if ($offset < 0 && $step < 0) {
                break;
            }

            try {

                if (is_null($value[$offset] ?? null)) {
                    continue;
                }

                $result[] = $value[$offset];
            } catch (Throwable) {

            }
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
