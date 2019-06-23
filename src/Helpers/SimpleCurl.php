<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Helpers;

use NGSOFT\Tools\Interfaces\CacheAble;

if (!function_exists('curl_init')) {

    throw new Exception();
}

class SimpleCurl {

    /** where to get certs */
    const CACERT_SRC = 'https://curl.haxx.se/ca/cacert.pem';

    /** Mozilla Firefox ESR 60 */
    const DEFAULT_USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0";

    /** @var resource */
    private $ch;

    /** @var string */
    private $userAgent = static::DEFAULT_USER_AGENT;

    /** @var int */
    private $timeout = 15;

    /** @var array<string,string> */
    private $headers;

    /**
     * Where to store the certs (folder)
     * @var string
     */
    private $certlocation = __DIR__ . "/../Data";

    public function __construct() {

    }

}
