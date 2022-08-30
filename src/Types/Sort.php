<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use NGSOFT\Enums\EnumTrait;

enum Sort
{

    use EnumTrait;

    case ASC;
    case DESC;

}
