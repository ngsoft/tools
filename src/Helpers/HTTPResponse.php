<?php

namespace NGSOFT\Tools\Helpers;

use InvalidArgumentException;
use NGSOFT\Tools\{
    Helpers\BasicStream, Interfaces\CurlHelper
};
use Psr\Http\Message\{
    ResponseInterface, StreamInterface
};
use function NGSOFT\Tools\array_every;

class HTTPResponse implements ResponseInterface, CurlHelper {

    /** @var int */
    private $status = 0;

    /** @var string|null */
    private $phrase;

    /** @var StreamInterface|null */
    private $body;

    /** @var string */
    private $protocol;

    /** @var array<string,array> */
    private $headers;

    /** @var array<string,string> */
    private $hmap = [];

    public function __construct(array $data) {

        $this->status = $data["status"] ?? 0;
        $this->phrase = $data["phrase"] ?? null;
        $this->body = $data["body"] instanceof StreamInterface ? $data["body"] : BasicStream::createStream();
        $protocol = $data["protocol"] ?? "1.1";
        $this->protocol = preg_match('/^[0-9](\.[0-9])?$/', $protocol) ? $protocol : "1.1";
        $this->headers = $data["headers"] ?? [];

        foreach (array_keys($this->headers) as $key) {
            $this->hmap[strtolower($key)] = $key;
        }
    }

    ////////////////////////////   ResponseInterface   ////////////////////////////

    /** {@inheritdoc} */
    public function getReasonPhrase() {
        if (!is_string($this->phrase)) return self::REASON_PHRASES[$this->getStatusCode()] ?? self::UNASSIGNED_REASON_PHRASE;
        return $this->phrase;
    }

    /** {@inheritdoc} */
    public function getStatusCode(): int {
        return $this->status;
    }

    /** {@inheritdoc} */
    public function withStatus($code, $reasonPhrase = '') {
        if (!is_int($code) or $code < 100 or $code >= 600) {
            throw new InvalidArgumentException('Status code must be an integer value between 1xx and 5xx.');
        }
        if ($code === $this->code && $reasonPhrase === ($this->phrase ?: "")) return $this;
        $c = clone $this;
        $c->status = $code;
        if (!empty($reasonPhrase) && is_string($reasonPhrase)) $c->phrase = $reasonPhrase;
        return $c;
    }

    ////////////////////////////   MessageInterface   ////////////////////////////

    /** {@inheritdoc} */
    public function getBody(): StreamInterface {
        return $this->body ?? ($this->body = BasicStream::createStream());
    }

    /** {@inheritdoc} */
    public function withBody(StreamInterface $body) {
        if ($body === $this->body) return $this;
        $c = clone $this;
        $c->body = $body;
        return $body;
    }

    /** {@inheritdoc} */
    public function getProtocolVersion() {
        return $this->protocol;
    }

    /** {@inheritdoc} */
    public function withProtocolVersion($version) {
        assert(is_string($version) && preg_match('/^[0-9](\.[0-9])?$/', $version) > 0);
        if ($version === $this->protocol) return $this;
        $c = clone $this;
        $c->protocol = $version;
        return $c;
    }

    /** {@inheritdoc} */
    public function getHeaders() {
        return $this->headers;
    }

    /** {@inheritdoc} */
    public function getHeader($name) {
        assert(is_string($name) and strlen($name) > 0);
        if (( $name = $this->hmap[strtolower($name)] ?? null)) {
            return $this->headers[$name];
        }
        return [];
    }

    /** {@inheritdoc} */
    public function getHeaderLine($name) {
        return implode(', ', $this->getHeader($name));
    }

    /** {@inheritdoc} */
    public function hasHeader($name) {
        $this->validateHeaderName($name);
        return ( $name = $this->hmap[strtolower($name)] ?? null) !== null;
    }

    public function withAddedHeader($name, $value) {
        $this->validateHeaderName($name);
        if (!$this->hasHeader($name)) return $this->withHeader($name, $value);
        $this->validateHeaderValue($value);

        $c = clone $this;
        $norm = strtolower($name);
        $name = $c->hmap[$norm];
        $value = array_map("trim", (!is_array($value) ? [$value] : $value));
        $c->headers[$name] = array_merge($c->headers[$name], $value);
        return $c;
    }

    /** {@inheritdoc} */
    public function withHeader($name, $value) {
        $this->validateHeaderName($name);
        $this->validateHeaderValue($value);
        $c = clone $this;
        $value = array_map("trim", (!is_array($value) ? [$value] : $value));
        $norm = strtolower($name);
        if ($this->hasHeader($name)) $name = $this->hmap[$norm];
        else $c->hmap[$norm] = $name;
        $c->headers[$name] = $value;
        return $c;
    }

    public function withoutHeader($name) {

        if (!$this->hasHeader($name)) return $this;
        $norm = strtolower($name);
        $name = $this->hmap[$norm];
        $c = clone $this;
        unset($c->headers[$name]);
        unset($c->hmap[$norm]);
        return $c;
    }

    private function validateHeaderName($name) {
        if (!is_string($name) or $name === "") throw new InvalidArgumentException("Invalid Header Name");
    }

    private function validateHeaderValue($value) {
        if (is_string($value) and strlen($value) > 0) return;
        if (is_array($value)) $valid = array_every("is_string", $value);
        else $valid = false;
        if (!$valid) throw new InvalidArgumentException("Invalid Header Value");
    }

}
