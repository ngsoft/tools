<?php

namespace NGSOFT\Tools\Helpers;

use NGSOFT\Tools\Interfaces\ArrayAccess;
use NGSOFT\Tools\Traits\ArrayAccessTrait;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 * @property-read string $url
 * @property-read string $content_type
 * @property-read int $http_code
 * @property-read int $header_size
 * @property-read int $request_size
 * @property-read int $redirect_count
 * @property-read double $total_time
 * @property-read double $namelookup_time
 * @property-read double $connect_time
 * @property-read double $pretransfer_time
 * @property-read int $size_upload
 * @property-read int $size_download
 * @property-read int $speed_download
 * @property-read double $starttransfer_time
 * @property-read double $redirect_time
 * @property-read string $redirect_url
 * @property-read string $primary_ip
 * @property-read int $primary_port
 * @property-read string $local_ip
 * @property-read int $local_port
 * @property-read int $http_version
 * @property-read int $protocol
 * @property-read string $scheme
 * @property-read int $appconnect_time_us
 * @property-read int $connect_time_us
 * @property-read int $namelookup_time_us
 * @property-read int $pretransfer_time_us
 * @property-read int $redirect_time_us
 * @property-read int $starttransfer_time_us
 * @property-read int $total_time_us
 * @property-read array $headers
 * @property-read string $error
 * @property-read int $errno
 */
class BasicCurlResponse implements ArrayAccess {

    use ArrayAccessTrait;

    /** @var resource */
    private $stream;

    public function __construct($ch, array $responseHeaders, $data) {

        $this->stream = $data;
        $info = $this->storage = (array) curl_getinfo($ch);
        $this->storage["errno"] = curl_errno($ch);
        $this->storage["error"] = curl_error($ch);

        $reqh = $this->parseHeaders($info["request_header"] ?? "");
        $resph = [];
        foreach ($responseHeaders as $headers) {
            $resph[] = $this->parseHeaders($headers);
        }
        $this->storage["headers"] = [
            "request" => $reqh,
            "response" => $resph
        ];


        print_r($this->storage);

        curl_close($ch);

        exit;
    }

    /** {@inheritdoc} */
    public function __destruct() {
        //fclose($this->stream);
    }

    /**
     * @return array
     */
    public function getRequestHeaders(): array {
        return $this->storage["headers"]["request"];
    }

    /**
     * @return array
     */
    public function getResponseHeaders(): array {
        return $this->storage["headers"]["response"];
    }

    /**
     * @return string
     */
    public function getContents(): string {
        rewind($this->stream);
        $contents = stream_get_contents($this->steam);
        return $contents ?? "";
    }

    /**
     *
     * @staticvar string $pattern
     * @param mixed $headers
     * @return array
     */
    protected function parseHeaders($headers): array {
        static $pattern = '/(?:(\S+):\s(.*))/';
        if (!is_array($headers)) $headers = explode("\n", trim($headers));
        $res = [];
        foreach ($headers as $header) {
            if (preg_match($pattern, $header, $matches)) {
                list(, $k, $v) = $matches;
                $res[$k] = trim($v);
            } else $res[] = trim($header);
        }
        return $res;
    }

    /** {@inheritdoc} */
    public function __get(string $prop) {
        return $this->offsetGet($prop);
    }

    /** {@inheritdoc} */
    public function offsetSet($o, $v) {

    }

    /** {@inheritdoc} */
    public function offsetUnset($o) {

    }

}
