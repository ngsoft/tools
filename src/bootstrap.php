<?php

declare(strict_types=1);

use NGSOFT\Tools;

// Set the internal encoding
mb_internal_encoding("UTF-8");

// Some Handy Constants that are not defined in PHP but should be.

@define('MINUTE', Tools::MINUTE);
@define('HOUR', Tools::HOUR);
@define('DAY', Tools::DAY);
@define('WEEK', Tools::WEEK);
@define('YEAR', Tools::YEAR);
@define('MONTH', Tools::MONTH);

@define('KB', Tools::KB);
@define('MB', Tools::MB);
@define('GB', Tools::GB);
@define('TB', Tools::TB);

@define('NAMESPACE_SEPARATOR', '\\');



