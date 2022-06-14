<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use NGSOFT\{
    Enums\Enum, Tools
};
use const NAMESPACE_SEPARATOR;

/**
 * @method static static YEAR()
 * @method static static MONTH()
 * @method static static WEEK()
 * @method static static DAY()
 * @method static static HOUR()
 * @method static static MINUTE()
 * @method static static SECOND()
 * @method static static MILLISECOND()
 * @method static static MICROSECOND()
 */
class Units extends Enum
{

    public const YEAR = 'years';
    public const MONTH = 'months';
    public const WEEK = 'weeks';
    public const DAY = 'days';
    public const HOUR = 'hours';
    public const MINUTE = 'min';
    public const SECOND = 'sec';
    public const MILLISECOND = 'ms';
    public const MICROSECOND = 'µs';

    public function getStep(): int|float
    {
        return constant(Tools::class . '::' . $this->name);
    }

    public function getPlural(): string
    {
        return $this->value;
    }

    public function getSingular(): string
    {
        if (str_ends_with($this->value, 's') && mb_strlen($this->value) > 2) {
            return substr($this->value, 0, -1);
        }

        return $this->value;
    }

}
