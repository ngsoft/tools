<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use DateInterval,
    HRTime\Unit,
    NGSOFT\DataStructure\Map,
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

    /** @var Map<State, array> */
    protected Map $infos;

    public static function create(int|float $seconds = 0): static
    {
        return new static($seconds);
    }

    public function __construct(protected readonly int|float $seconds)
    {

        $infos = $this->infos = new Map();
        $remaining = $seconds;
        /** @var Units $unit */
        foreach (Units::cases() as $unit) {
            $step = $unit->getStep();

            $count = (int) floor($remaining / $step);
            $remaining -= $step * $count;
            $infos[$unit] = [
                'absolute' => $seconds / $step,
                'relative' => $count,
            ];
        }
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

        return $this->formats;
    }

    public function __toString()
    {
        return sprintf('%s', (string) $this->getSeconds());
    }

    public function __debugInfo(): array
    {
        return [
            'raw' => $this->getRaw(),
            'infos' => $this->infos,
            'formated' => $this->__toString(),
            'str' => $this->getFormatedTime(),
            DateInterval::class => $this->getDateInterval(),
        ];
    }

    protected function getUnit(string|Unit $name, bool $relative = false): int|float
    {

        if (!is_string($name)) {
            $name = $name->value;
        }

        return $relative ? $this->infos[$name] ['relative'] : $this->infos[$name] ['absolute'];
    }

    protected function getFormatedTime(): string
    {

        $result = [];
        $steps = [];
        /** @var Units $unit */
        foreach (Units::cases() as $unit) {

            if ($count = $this->infos[$unit->value]['relative']) {
                $formated = $count > 1 ? $unit->getPlural() : $unit->getSingular();
                $steps[] = sprintf("%d %s", $count, $formated);
            }
        }

        $str = trim(implode(' ', $steps));
        if (empty($str)) {
            return '0 sec';
        }

        return $str;
    }

}
