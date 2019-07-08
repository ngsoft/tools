<?php

namespace NGSOFT\Tools\Helpers;

use InvalidArgumentException,
    NGSOFT\Tools\Interfaces\CurlHelper;
use Psr\Http\Message\{
    ResponseFactoryInterface, ResponseInterface, StreamInterface
};

class HTTPResponse implements ResponseInterface, CurlHelper {

    /** @var int */
    private $status = 0;

    /** @var string|null */
    private $phrase;

    /** @var StreamInterface|null */
    private $body;

    ////////////////////////////   ResponseInterface   ////////////////////////////


    public static function create(array $data = []) {
        $instance = new static();
        return $instance;
    }

    /** {@inheritdoc} */
    public function getReasonPhrase(): string {
        if (!is_string($this->phrase)) return self::REASON_PHRASES[$this->getStatusCode()] ?? self::UNASSIGNED_REASON_PHRASE;
        return $this->phrase;
    }

    /**
     * @internal Set the Reason Phrase
     * @param string $reasonPhrase
     */
    public function setReasonPhrase(string $reasonPhrase) {
        $this->phrase = !empty($reasonPhrase) ? $reasonPhrase : null;
    }

    /** {@inheritdoc} */
    public function getStatusCode(): int {
        return $this->code;
    }

    /**
     * @internal Set the status Code
     * @throws InvalidArgumentException
     * @param int $code
     */
    public function setStatusCode(int $code) {
        if ($code < 100 or $code > 999) throw new InvalidArgumentException("Invalid Code $code");
        $this->code = $code;
    }

    /** {@inheritdoc} */
    public function withStatus($code, $reasonPhrase = '') {
        $i = clone $this;
        $i->setStatusCode($code);
        $i->setReasonPhrase($reasonPhrase);
        return $i;
    }

    ////////////////////////////   MessageInterface   ////////////////////////////


    public function setBody(StreamInterface $body) {
        $this->body = $body;
    }

    /** {@inheritdoc} */
    public function getBody(): StreamInterface {
        return $this->body ?? BasicStream::helper()->createStream();
    }

    public function withBody(StreamInterface $body) {

    }

    public function getHeader($name) {

    }

    public function getHeaderLine($name): string {

    }

    public function getHeaders() {

    }

    public function getProtocolVersion(): string {

    }

    public function hasHeader($name): bool {

    }

    public function withAddedHeader($name, $value) {

    }

    public function withHeader($name, $value) {

    }

    public function withProtocolVersion($version) {

    }

    public function withoutHeader($name) {

    }

}
