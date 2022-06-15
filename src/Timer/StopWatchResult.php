<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use DateInterval,
    NGSOFT\DataStructure\Map,
    Stringable;
use const NGSOFT\Tools\{
    DAY, HOUR, MICROSECOND, MILLISECOND, MINUTE, MONTH, SECOND, WEEK, YEAR
};
use function NGSOFT\Tools\map;

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
        return DateInterval::createFromDateString($this->getFormatedString());
    }

    public function getRaw(): int|float
    {
        return $this->seconds;
    }

    public function getDateFormat(string $format): string
    {
        return $this->getDateInterval()->format($format);
    }

    public function getYears(int $precision = 0): int|float
    {
        return $this->getUnitValue(Units::YEAR(), $precision);
    }

    public function getMonths(int $precision = 0): int|float
    {
        return $this->getUnitValue(Units::MONTH(), $precision);
    }

    public function getWeeks(int $precision = 0): int|float
    {
        return $this->getUnitValue(Units::WEEK(), $precision);
    }

    public function getDays(int $precision = 0): int|float
    {
        return $this->getUnitValue(Units::DAY(), $precision);
    }

    public function getHours(int $precision = 0): int|float
    {
        return $this->getUnitValue(Units::HOUR(), $precision);
    }

    public function getMinutes(int $precision = 0): int|float
    {
        return $this->getUnitValue(Units::MINUTE(), $precision);
    }

    protected function getUnitValue(Units $unit, int $precision, bool $relative = false): int|float
    {
        $key = $relative ? 'relative' : 'absolute';
        $result = $this->infos->get($unit)[$key];
        if ($relative) {
            return $result;
        }

        if ($precision === 0) {
            return (int) floor($result);
        }
        return round($result, $precision);
    }

    public function getSeconds(int $precision = 3): int|float
    {

        $result = round($this->seconds, $precision);
        if ($precision === 0) {
            $result = (int) $result;
        }
        return $result;
    }

    public function getMilliseconds(int $precision = 2): int|float
    {

        $result = round($this->seconds / MILLISECOND, $precision);
        if ($precision === 0) {
            $result = (int) $result;
        }

        return $result;
    }

    public function getMicroseconds(bool $asFloat = true): int|float
    {
        $result = round($this->seconds / MICROSECOND);

        return $asFloat ? $result : (int) $result;
    }

    public function getFormatedString(): string
    {

        $result = [];
        $steps = [];
        /** @var Units $unit */
        foreach (Units::cases() as $unit) {

            if ($count = $this->infos[$unit]['relative']) {
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

    public function toArray(): array
    {

        return map(function ($value, &$key) {
            $key = $key->value;
            return $value['relative'];
        }, $this->infos);
    }

    public function __toString()
    {
        return sprintf('%s', (string) $this->getSeconds());
    }

    public function __debugInfo(): array
    {

        $infos = [];

        foreach ($this->infos as $enum => $value) {

            $key = sprintf('enum(%s::%s)', $enum::class, $enum->name);

            $infos[$key] = $value;
        }

        return [
            'raw' => $this->getRaw(),
            'infos' => $infos,
            'formated' => $this->__toString(),
            'str' => $this->getFormatedTime(),
            DateInterval::class => $this->getDateInterval(),
        ];
    }

}
