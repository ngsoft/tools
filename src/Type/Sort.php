<?php

declare(strict_types=1);

namespace NGSOFT\Type;

use NGSOFT\Enums\EnumTrait;

enum Sort
{

    use EnumTrait;

    case ASC;
    case DESC;

}
