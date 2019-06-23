<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Helpers;

use stdClass;
use UI\Exception\InvalidArgumentException;
use UI\Exception\RuntimeException;
use function NGSOFT\Tools\validUrl;

if (!function_exists('curl_init')) {

    throw new RuntimeException("Curl Extension not loaded.");
}

class BasicCurlRequest {

    /** where to get certs */
    const CACERT_SRC = 'https://curl.haxx.se/ca/cacert.pem';
    const CURL_DEFAULTS = [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_ENCODING => "gzip,deflate",
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLINFO_HEADER_OUT => true,
        /** @link https://curl.haxx.se/libcurl/c/CURLOPT_COOKIEFILE.html Enables cookie engine without using a file */
        CURLOPT_COOKIEFILE => ""
    ];

    /** Mozilla Firefox ESR 60 */
    const DEFAULT_USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0";

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

    /** @var array */
    private $opts = [];

    /** @var string */
    private $cookieFile = "";

    ////////////////////////////   Builder   ////////////////////////////

    /**
     * Add Json data to post
     * @link https://lornajane.net/posts/2011/posting-json-data-with-php-curl
     * @param string $json
     * @return static
     */
    public function postJson(string $json) {
        $this;
        return $this->addHeaders([
                    "Content-Type" => "application/json",
                    "Content-Lengt" => strlen($json)
                ])->addOpts([
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $json
        ]);
    }

    /**
     * Post data as key value pairs
     * @param array $data
     * @return static
     */
    public function postData(array $data) {
        return $this->addOpts([
                    CURLOPT_POSTREDIR => CURL_REDIR_POST_ALL,
                    CURLOPT_POSTFIELDS => http_build_query($data)
        ]);
    }

    /**
     * Set a cookie location for that request
     * @param string $cookieFile File where will be stored the cookies
     * @return static
     * @throws InvalidArgumentException
     */
    public function setCookieFile(string $cookieFile) {
        $dirname = dirname($cookieFile);
        if (!is_dir($dirname) or ! is_writable($dirname)) {
            throw new InvalidArgumentException("$dirname for cookie file does not exists or is not writable.");
        }
        $this->cookieFile = $cookieFile;
        return $this;
    }

    /**
     * Set User Agen for the Request
     * @param string $userAgent
     * @return static
     */
    public function setUserAgent(string $userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Set Request and Connection timeout for the request
     * @param type $timeout
     * @return static
     */
    public function setTimeout(int $timeout) {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Set the Certifications download folder
     * @param string $certlocation
     * @return static
     * @throws InvalidArgumentException
     */
    public function setCertlocation(string $certlocation) {
        if (!is_dir($certlocation) or ! is_writable($certlocation)) {
            throw new InvalidArgumentException("$certlocation is not an existing directory or is not writable.");
        }
        $this->certlocation = $certlocation;
        return $this;
    }

    /**
     * Add a single header to the stack
     * @param string $key
     * @param string $value
     * @return static
     */
    public function addHeader(string $key, string $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Add multiple headers to the stack
     * @param array<string,string> $keyValuePair
     * @return static
     */
    public function addHeaders(array $keyValuePair) {
        foreach ($keyValuePair as $k => $v) {

            $this->addHeader($k, $v);
        }
        return $this;
    }

    /**
     * Add an option to curl
     * @param int $curlopt CURLOPT_...
     * @param mixed $value
     * @return static
     */
    public function addOpt(int $curlopt, $value) {
        $this->opts[$curlopt] = $value;
        return $this;
    }

    /**
     * Add Multiples options to curl
     * @param array $options
     * @return $this
     */
    public function addOpts(array $options) {
        foreach ($options as $k => $v) {
            $this->addOpt($k, $v);
        }
        return $this;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Encode key value pairs to a valid curl input
     * @return array
     */
    protected function makeHeaders(): array {
        $lines = [];
        foreach ($this->headers as $k => $v) {
            $lines[] = sprintf('%s: %s');
        }
        return $lines;
    }

    /**
     * Creates a curl Handle with default values
     * @return resource
     */
    protected function curlinit(): resource {
        $ch = curl_init();
        //Basic Opts working with all requests
        if (count($this->headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt_array($ch, static::CURL_DEFAULTS);
        curl_setopt_array($ch, [
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERAGENT => $this->userAgent
        ]);
        //enables cookies r/w
        if (!empty($this->cookieFile)) {
            curl_setopt_array($ch, [
                CURLOPT_COOKIEFILE => $this->cookieFile,
                CURLOPT_COOKIEJAR => $this->cookieFile
            ]);
        }
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        if ($capath = $this->getCA())
                curl_setopt_array($ch, [
                CURLOPT_CAINFO => $capath,
                CURLOPT_SSL_VERIFYPEER => true
            ]);
        else curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (count($this->opts)) curl_setopt_array($ch, $this->opts);
        return $ch;
    }

    /**
     * Execute curl request
     * @param resource $ch
     * @param string|null $url
     * @return stdClass
     * @throws InvalidArgumentException
     */
    protected function curlExec(resource $ch, string $url = null): stdClass {
        if ($url = $url ?? curl_getinfo($ch, $opt)["url"]) {
            //check if valid url
            if (!$this->validateUrl($url)) throw new InvalidArgumentException("Url is not valid.");
            curl_setopt($ch, CURLOPT_URL, $url);
            $headers = [];
            $index = 0;
            $getheaders = function($c, $header) use (&$headers, &$index) {
                $len = strlen($header);
                if (empty($header[$index])) {
                    $headers[$index] = [];
                }
                $clean = trim($header);
                if (empty($clean)) {
                    $index++;
                } else {
                    $headers[$index][] = $clean;
                }
                return $len;
            };
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, $getheaders);
            $data = curl_exec($ch);
            $return = (object) [
                        "error" => curl_error($ch),
                        "errno" => curl_errno($ch),
                        "info" => (object) curl_getinfo($ch),
                        "request_headers" => explode("\n", trim(curl_getinfo($ch, CURLINFO_HEADER_OUT))),
                        "response_headers" => $headers,
                        "data" => $data
            ];
            //? curl_close($ch);
            return $return;
        }
        throw new InvalidArgumentException("Url not set cannot process request.");
    }

    /**
     * Downloads Certifications from haxx (if not already present)
     * @staticvar string $path
     * @return string|null
     */
    protected function getCA() {
        static $path = null;
        if ($path === null) {
            $file = sprintf("%s/%s", $this->certlocation, basename(self::CACERT_SRC));
            if (!is_file($file)) {

                if ($fileh = fopen($file, 'w')) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_ENCODING => 'gzip,deflate',
                        CURLOPT_URL => static::CACERT_SRC,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_FILE => $fileh
                    ]);
                    $response = $this->curlexec($ch);
                    fclose($fileh);
                    return $response->error ? null : $path = realpath($file);
                }
            } else $path = $file;
        }
        return $path;
    }

    /**
     * Checks if URL is valid
     * @param string $url
     * @return bool
     */
    protected function validateUrl(string $url): bool {
        return validUrl($url);
    }

}
