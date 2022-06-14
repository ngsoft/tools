<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use Stringable;
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

    public function microseconds(): int|float
    {
        return round($this->seconds * 1e+6);
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
            'seconds' => $this->seconds(),
            'milliseconds' => $this->milliseconds(),
            'microseconds' => $this->microseconds(),
            'formated' => $this->__toString(),
            'array' => $this->toArray(),
        ];
    }

    function getFilesize(int|float $size, int $precision = 2): string
    {
        static $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;
        while (($size / $step) >= 1) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision) . $units[$i];
    }

    protected function formatTime(int|float $input, array &$result = null): string
    {

        static $units = [
            'Y' => [YEAR, '%d year', '%d years'],
            'M' => [MONTH, '%d month', '%d months'],
            'W' => [WEEK, '%d week', '%d weeks'],
            'D' => [DAY, '%d day', '%d days'],
            'h' => [HOUR, '%d hours', '%d hours'],
            'm' => [MINUTE, '%d min'],
            's' => [SECOND, '%d sec'],
            'ms' => [MILLISECOND, '%d ms']
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
