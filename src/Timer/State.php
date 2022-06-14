<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use NGSOFT\Enums\Enum;

/**
 * @method static static IDLE()
 * @method static static STARTED()
 * @method static static PAUSED()
 * @method static static STOPPED()
 */
class State extends Enum
{

    public const IDLE = 0;
    public const STARTED = 1;
    public const PAUSED = 2;
    public const STOPPED = 3;

}
