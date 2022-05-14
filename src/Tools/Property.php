<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

class Property {

    private const VALID_PROPERTY_NAME = '/^[a-z][\w\-]+/i';

    /** @var string */
    private $name;

    /** @var bool */
    private $writable = false;

    /** @var bool */
    private $configurable = true;

    /** @var bool */
    private $enumerable = false;

    /** @var mixed */
    private $value;

}
