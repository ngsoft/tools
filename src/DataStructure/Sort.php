<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Enums\EnumTrait;

enum Sort: int
{

    use EnumTrait;

    case ASC = 0;
    case DESC = 1;

}
