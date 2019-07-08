<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Helpers;

use NGSOFT\Tools\Exceptions\{
    InvalidArgumentException, RuntimeException
};
use Psr\Http\Message\{
    StreamFactoryInterface, StreamInterface
};
use Throwable;
use function NGSOFT\Tools\safe_exec;

class BasicStream implements StreamInterface, StreamFactoryInterface {
    ////////////////////////////   StreamFactoryInterface   ////////////////////////////

    /** @var StreamFactoryInterface */
    private static $staticInstance;

    /** @return StreamFactoryInterface */
    public static function helper(): StreamFactoryInterface {
        if (!isset(self::$staticInstance)) {
            self::$staticInstance = new static(fopen("php://temp", "r"));
            self::$staticInstance->detach();
        }
        return self::$staticInstance;
    }

    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content String content with which to populate the stream.
     *
     * @return StreamInterface
     */
    public function createStream(string $content = ''): StreamInterface {
        $handle = fopen("php://temp", "w+");
        $stream = new static($handle);
        $stream->write($content);
        return $stream;
    }

    /** {@inheritdoc} */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface {
        if (!preg_match(self::VALID_STREAM_MODES, $mode)) throw new InvalidArgumentException("Mode $mode is invalid.");
        $handle = safe_exec(function ($fn, $m) {
            return fopen($fn, $m);
        }, $filename, $mode);

        if ($handle === false) throw new RuntimeException("The file $filename cannot be opened.");
        return new static($handle);
    }

    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource PHP resource to use as basis of stream.
     *
     * @return StreamInterface
     */
    public function createStreamFromResource($resource): StreamInterface {
        assert(is_resource($resource));
        return new static($resource);
    }

    ////////////////////////////   StreamInterface   ////////////////////////////


    const VALID_STREAM_MODES = '/^[rwxacbt+]{1,3}$/';
    const READABLE_STREAM_HASH = [
        'r', 'w+', 'r+', 'x+', 'c+', 'rb', 'w+b', 'r+b', 'x+b',
        'c+b', 'rt', 'w+t', 'r+t', 'x+t', 'c+t', 'a+', 'rb+',
    ];
    const WRITABLE_STREAM_HASH = [
        'w', 'w+', 'rw', 'r+', 'x+', 'c+', 'wb', 'w+b', 'r+b',
        'rb+', 'x+b', 'c+b', 'w+t', 'r+t', 'x+t', 'c+t', 'a', 'a+'
    ];
    const DEFAULT_OPTIONS = [
        "size" => null,
        "meta" => []
    ];

    /** @var resource|null */
    protected $resource;

    /** @var bool */
    protected $readable = false;

    /** @var bool */
    protected $writable = false;

    /** @var bool */
    protected $seekable = false;

    /** @var int|null */
    protected $size;

    /** @var string|null */
    protected $uri;

    /** @var array */
    protected $meta;

    /** @var bool */
    protected $closed = false;

    /**
     * @param resource $resource
     * @param array $options
     */
    public function __construct($resource, array $options = []) {
        assert(is_resource($resource));
        $options = array_merge([], self::DEFAULT_OPTIONS, $options);
        if (isset($options["size"])) $this->size = is_int($options["size"]) ? $options["size"] : null;
        $this->resource = $resource;
        $meta = stream_get_meta_data($resource);
        $this->seekable = $meta["seekable"];
        $this->readable = in_array($meta["mode"], self::READABLE_STREAM_HASH);
        $this->writable = in_array($meta["mode"], self::WRITABLE_STREAM_HASH);
        $this->meta = $options["meta"] ?? [];
        $this->uri = $this->getMetadata("uri");
    }

    /**
     * Closes the stream
     */
    public function __destruct() {
        $this->close();
    }

    /** {@inheritdoc} */
    public function getResource() {
        return $this->resource;
    }

    /** {@inheritdoc} */
    public function __toString() {
        try {
            $this->seek(0);
            return stream_get_contents($this->resource);
        } catch (Throwable $ex) {
            $ex->getCode();
            return "";
        }
    }

    /** {@inheritdoc} */
    public function close() {
        if (!$this->closed and isset($this->resource)) fclose($this->resource);
        $this->closed = true;
    }

    /** {@inheritdoc} */
    public function detach() {
        $stream = $this->resource ?? null;
        $this->resource = null;
        $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = $this->closed = false;
        return $stream;
    }

    /** {@inheritdoc} */
    public function getSize() {
        if ($this->size !== null) return $this->size;
        if (isset($this->resource)) {
            isset($this->uri) && clearstatcache(true, $this->uri);
            $stats = fstat($this->resource);
            return $this->size = $stats["size"] ?? null;
        }
        return null;
    }

    /** {@inheritdoc} */
    public function tell() {
        if (isset($this->resource)) {
            $return = ftell($this->resource);
            if ($return === false) throw new RuntimeException("Unable to determine the stream position.");
            return $return;
        }
        throw new RuntimeException("Stream is detached.");
    }

    /** {@inheritdoc} */
    public function eof() {
        if (isset($this->resource)) {
            return feof($this->resource);
        }
        throw new RuntimeException("Stream is detached.");
    }

    /** {@inheritdoc} */
    public function isSeekable() {
        return $this->seekable;
    }

    /** {@inheritdoc} */
    public function seek($offset, $whence = SEEK_SET) {
        if (isset($this->resource)) {
            if ($this->seekable === true) {
                if (fseek($this->resource, $offset, $whence) === -1) {
                    throw new RuntimeException("Unable to seek stream for offset $offset with whence " . var_export($whence, true));
                }
                return;
            }
            throw new RuntimeException("Stream is not seekable.");
        }
        throw new RuntimeException("Stream is detached.");
    }

    /** {@inheritdoc} */
    public function rewind() {
        $this->seek(0);
    }

    /** {@inheritdoc} */
    public function isWritable() {
        return $this->writable;
    }

    /** {@inheritdoc} */
    public function write($string) {
        assert(is_string($string));
        if (isset($this->resource)) {
            if ($this->writable === true) {
                $this->size = null;
                $result = fwrite($this->resource, $string);
                if ($result === false) throw new RuntimeException("Unable to write on that stream.");
                return $result;
            }
            throw new RuntimeException("Cannot write on that stream.");
        }
        throw new RuntimeException("Stream is detached.");
    }

    /** {@inheritdoc} */
    public function isReadable() {
        return $this->readable;
    }

    /** {@inheritdoc} */
    public function read($length) {
        assert(is_int($length));
        if (isset($this->resource)) {
            if ($this->readable === true) {
                if ($length < 0) throw new RuntimeException("Length cannot be negative : $length.");
                if ($length === 0) return "";
                $result = fread($this->resource, $length);
                if ($result === false) throw new RuntimeException("Unable to read on that stream.");
                return $result;
            }
            throw new RuntimeException("Cannot read on that stream.");
        }
        throw new RuntimeException("Stream is detached.");
    }

    /** {@inheritdoc} */
    public function getContents() {
        if (isset($this->resource)) {
            $contents = stream_get_contents($this->resource);
            if ($contents === false) {
                throw new RuntimeException("Unable to read on that stream.");
            }
            return $contents;
        }
        throw new RuntimeException("Stream is detached.");
    }

    /** {@inheritdoc} */
    public function getMetadata($key = null) {
        if (!isset($this->resource)) return is_string($key) ? null : [];
        $meta = array_merge(stream_get_meta_data($this->resource), $this->meta);
        return isset($key) ? $meta[$key] ?? null : $meta;
    }

}
