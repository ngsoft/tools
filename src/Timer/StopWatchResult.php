<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use DateInterval,
    Stringable;
use const NGSOFT\{
    DAY, HOUR, MICROSECOND, MILLISECOND, MINUTE, MONTH, SECOND, WEEK, YEAR
};

class StopWatchResult implements Stringable
{

    public static function create(int|float $seconds = 0): static
    {
        return new static($seconds);
    }

    public function __construct(protected readonly int|float $seconds)
    {

    }

    public function toDateInterval(): DateInterval
    {
        return DateInterval::createFromDateString('+' . $this->formatTime($this->seconds));
    }

    public function raw(): int|float
    {
        return $this->seconds;
    }

    public function seconds(int $precision = 3): int|float
    {
        $result = round($this->seconds, $precision);
        if ($precision === 0) {
            $result = (int) $result;
        }
        return $result;
    }

    public function milliseconds(int $precision = 2): int|float
    {
        $result = round($this->seconds * 1e+3, $precision);
        if ($precision === 0) {
            $result = (int) $result;
        }

        return $result;
    }

    public function microseconds(bool $asFloat = true): int|float
    {
        $result = round($this->seconds * 1e+6);

        return $asFloat ? $result : (int) $result;
    }

    public function toArray(): array
    {

        $this->formatTime($this->seconds, $result);
        return $result;
    }

    public function __toString()
    {
        return $this->formatTime($this->seconds);
    }

    public function __debugInfo(): array
    {

        $format = $this->formatTime($this->seconds, $r);

        return [
            'raw' => $this->raw(),
            'float' => [
                'seconds' => $this->seconds(),
                'milliseconds' => $this->milliseconds(),
                'microseconds' => $this->microseconds(),
            ],
            'int' => [
                'seconds' => $this->seconds(0),
                'milliseconds' => $this->milliseconds(0),
                'microseconds' => $this->microseconds(false),
            ],
            'formated' => $this->__toString(),
            'array' => $this->toArray(),
            DateInterval::class => $this->toDateInterval(),
        ];
    }

    protected function formatTime(int|float $input, array &$result = null): string
    {
        /**
         * @link https://www.php.net/manual/en/datetime.formats.relative.php
         */
        static $units = [
            'years' => [YEAR, '%d year', '%d years'],
            'months' => [MONTH, '%d month', '%d months'],
            'weeks' => [WEEK, '%d week', '%d weeks'],
            'days' => [DAY, '%d day', '%d days'],
            'hours' => [HOUR, '%d hours', '%d hours'],
            'min' => [MINUTE, '%d min'],
            'sec' => [SECOND, '%d sec'],
            'ms' => [MILLISECOND, '%d ms'],
            'µs' => [MICROSECOND, '%d µs']
        ];

        $result = [];

        $remaining = $input;

        $steps = [];

        foreach ($units as $name => list($step, $singular, $plural, $short)) {

            if (!$short) {
                $short = $plural;
                $plural = $singular;
                if (!$short) {
                    $short = $plural;
                }
            }

            $count = floor($remaining / $step);

            if ($count >= 1) {
                $remaining -= $count * $step;

                $format = $count > 1 ? $plural : $singular;
                if ($getshort) {
                    $format = $short;
                }


                $steps[] = sprintf($format, $count);
                $result[$name] = (int) $count;
                continue;
            } else { $result[$name] = 0; }
        }


        $str = trim(implode(' ', $steps));
        if (empty($str)) {
            return '0 sec';
        }

        return $str;
    }

}
