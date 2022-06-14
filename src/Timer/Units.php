<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use NGSOFT\Enums\{
    Enum, EnumUtils
};

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

}
