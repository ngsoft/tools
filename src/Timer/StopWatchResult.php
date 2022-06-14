<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use DateInterval,
    Stringable;
use const NGSOFT\Tools\{
    DAY, HOUR, MICROSECOND, MILLISECOND, MINUTE, MONTH, SECOND, WEEK, YEAR
};

class StopWatchResult implements Stringable
{

    public const FORMAT_SECONDS = 0;
    public const FORMAT_MILLISECONDS = 1;
    public const FORMAT_MICROSECONDS = 2;
    public const FORMAT_HUMAN_READABLE = 3;

    /**
     * @link https://www.php.net/manual/en/datetime.formats.relative.php
     */
    static protected $units = [
        Units::YEAR => [YEAR, '%d year', '%d years'],
        Units::MONTH => [MONTH, '%d month', '%d months'],
        Units::WEEK => [WEEK, '%d week', '%d weeks'],
        Units::DAY => [DAY, '%d day', '%d days'],
        Units::HOUR => [HOUR, '%d hours', '%d hours'],
        Units::MINUTE => [MINUTE, '%d min'],
        Units::SECOND => [SECOND, '%d sec'],
        Units::MILLISECOND => [MILLISECOND, '%d ms'],
        Units::MICROSECOND => [MICROSECOND, '%d µs']
    ];

    /** @var array<string, int> */
    protected array $infos = [];

    public static function create(int|float $seconds = 0): static
    {
        return new static($seconds);
    }

    public function __construct(protected readonly int|float $seconds)
    {

    }

    public function getDateInterval(): DateInterval
    {
        return DateInterval::createFromDateString($this->getFormatedTime($this->seconds));
    }

    public function getRaw(): int|float
    {
        return $this->seconds;
    }

    public function getSeconds(int $precision = 3): int|float
    {

        $result = round($this->getUnit('sec'), $precision);
        if ($precision === 0) {
            $result = (int) $result;
        }
        return $result;
    }

    public function getMilliseconds(int $precision = 2): int|float
    {
        $result = round($this->seconds * 1e+3, $precision);
        if ($precision === 0) {
            $result = (int) $result;
        }

        return $result;
    }

    public function getMicroseconds(bool $asFloat = true): int|float
    {
        $result = round($this->seconds * 1e+6);

        return $asFloat ? $result : (int) $result;
    }

    public function format(int $format = self::FORMAT_HUMAN_READABLE): string
    {
        return $this->getFormatedTime();
    }

    public function toArray(): array
    {
        $this->lazyLoad();
        return $this->formats;
    }

    public function __toString()
    {
        return sprintf('%s', (string) $this->getSeconds());
    }

    public function __debugInfo(): array
    {


        $this->lazyLoad();

        return [
            'raw' => $this->getRaw(),
            'infos' => $this->infos,
            'formated' => $this->__toString(),
            'str' => $this->getFormatedTime(),
            DateInterval::class => $this->getDateInterval(),
        ];
    }

    protected function lazyLoad(): void
    {
        if (empty($this->infos)) {
            $seconds = $remaining = $this->seconds;
            foreach (self::$units as $name => list($step)) {
                $count = (int) floor($remaining / $step);
                $remaining -= $step * $count;
                $this->infos[$name] = [
                    'absolute' => $seconds / $step,
                    'relative' => $count,
                ];
            }
        }
    }

    protected function getUnit(string $name, bool $relative = false): int|float
    {
        $this->lazyLoad();
        return $relative ? $this->infos[$name] ['relative'] : $this->infos[$name] ['absolute'];
    }

    protected function getFormatedTime(): string
    {
        $units = &self::$units;
        $this->lazyLoad();

        $result = [];
        $steps = [];

        foreach ($units as $name => list($step, $singular, $plural)) {

            if ($count = $this->infos[$name]['relative']) {
                $plural = $plural ?? $singular;
                $format = $count > 1 ? $plural : $singular;
                $steps[] = sprintf($format, $count);
            }
        }

        $str = trim(implode(' ', $steps));
        if (empty($str)) {
            return '0 sec';
        }

        return $str;
    }

}
