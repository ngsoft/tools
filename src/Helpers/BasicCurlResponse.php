<?php

namespace NGSOFT\Tools\Helpers;

use NGSOFT\Tools\Interfaces\ArrayAccess;
use NGSOFT\Tools\Traits\ArrayAccessTrait;
use Psr\Http\Message\StreamInterface;

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
class BasicCurlResponse extends HTTPResponse implements ArrayAccess {

    use ArrayAccessTrait;

    public function __construct($ch, array $responseHeaders, StreamInterface $stream) {

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
        curl_close($ch);
        $pheaders = [];
        foreach ($resph as $r) {
            foreach ($r as $k => $v) {
                if (($k === 0) && preg_match('/HTTP\/([0-9](\.[0-9])?)/', $v, $m)) {
                    list(, $version) = $m;
                }
                if (is_string($k)) {
                    $pheaders[$k][] = trim($v);
                }
            }
        }

        parent::__construct([
            "status" => $this->http_code,
            "body" => $stream,
            "version" => $version ?? null,
            "headers" => $pheaders
        ]);
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
        return (string) $this->getStream();
    }

    /** @return StreamInterface */
    public function getStream(): StreamInterface {
        return $this->getBody();
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
