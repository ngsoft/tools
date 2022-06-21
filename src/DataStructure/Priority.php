<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Enums\EnumTrait;

enum Priority: int
{

    use EnumTrait;

    case HIGH = 128;
    case MEDIUM = 64;
    case LOW = 32;

}
