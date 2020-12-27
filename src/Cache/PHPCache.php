<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use JsonSerializable,
    Psr\Log\LoggerInterface,
    Serializable;
use function NGSOFT\Tools\{
    listFiles, normalizePath, rrmdir, safeInclude
};

/**
 * Caches data using var_export
 * Improved version of OPCache that uses less file hits and can store binaries (images ...)
 */
final class PHPCache extends CachePool {

    const FILE_TEMPLATE = '<?php return %s;';
    const FILE_EXTENSION = 'php';

    /** @var array<string,array> */
    private $meta = [];

    /** @var string|null */
    private $root;

    /** @var string */
    private $rootpath;

    /**
     * @Inject
     * @var LoggerInterface
     */
    private $logger;

    /** {@inheritdoc} */
    protected function doContains(string $key): bool {
        static $logged; //logs only once per page load
        if ($this->root === null) {
            //php-di or other container annotation injection
            if ($logged !== true && $this->logger instanceof LoggerInterface) {
                $logged = true;
                $this->logger->debug("Cannot write in cache location.", [
                    "class" => self::class,
                    "path" => $this->rootpath
                ]);
            }
            return false; // cache empty
        }
        $hash = $this->getHash($key);
        if (isset($this->meta[$hash])) {
            $meta = $this->meta[$hash];
            if (time() > $meta['expire']) {
                @unlink($meta['file']);
                unset($this->meta[$hash]);
                return false;
            }
            return is_file($meta['file']);
        }
        return false;
    }

    /** {@inheritdoc} */
    protected function doDelete(string $key): bool {
        $hash = $this->getHash($key);
        if (isset($this->meta[$hash])) {
            @unlink($this->meta[$hash]['file']);
            unset($this->meta[$hash]);
        }
        return true;
    }

    /** {@inheritdoc} */
    protected function doFetch(string $key): CacheItem {
        if (!$this->doContains($key)) return $this->createEmptyItem($key);
        $hash = $this->getHash($key);
        $file = $this->meta[$hash]['file'];
        $type = $this->meta[$hash]['type'];
        $contents = null;
        if ($type === "php") $contents = safeInclude($file);
        else if (is_file($file)) {
            $handle = @fopen($file, "r");
            @flock($handle, LOCK_SH);
            $contents = @fread($handle, @filesize($file));
            @flock($handle, LOCK_UN);
            @fclose($handle);
            if (!is_string($contents)) $contents = null;
        }
        return new CacheItem($key, $this->getTTL(), $contents !== null, $contents);
    }

    /** {@inheritdoc} */
    protected function doFlush(): bool {
        if ($this->root !== null && rrmdir($this->root)) return @mkdir($this->root, 0777, true);
        return true;
    }

    /** {@inheritdoc} */
    protected function doSave(CacheItem $item): bool {
        if ($this->root === null) return false;
        $expire = $item->getExpireAt()->getTimestamp();
        $key = $item->getKey();
        $hash = $this->getHash($key);
        $contents = $item->get();
        unset($this->meta[$hash]);
        return $this->doWrite($hash, $expire, $contents);
    }

    /**
     * Compile the cached file
     * @param string $hash
     * @param int $expire
     * @param mixed $data
     * @return bool
     */
    private function doWrite(string $hash, int $expire, $data): bool {
        if (in_array(($type = gettype($data)), ["unknown type", "resource", "resource (closed)", "NULL"])) return false;
        $type = $type === "string" ? "bin" : "php";
        if (is_object($data)) {

            if ($data instanceof Serializable) $value = sprintf("unserialize ('%s')", str_replace("'", "\'", serialize($data)));
            elseif (method_exists($data, '__set_state')) {
                if ($data instanceof JsonSerializable) $array = $data->jsonSerialize();
                elseif (method_exists($data, 'toArray')) $array = $data->toArray();
                else return false;
                $value = sprintf('%s::__set_state(%s)', get_class($data), var_export($array, true));
            } else return false; // cannot be certain to retrieve the same datas
            $value = sprintf(self::FILE_TEMPLATE, $value);
        } elseif ($type === "bin") $value = $data; //strings / binary strings
        else $value = sprintf(self::FILE_TEMPLATE, var_export($data, true)); // booleans, int, float

        set_time_limit(60);
        $filename = $this->root . DIRECTORY_SEPARATOR . sprintf('%s.%u.%s', $hash, $expire, $type);
        $handle = fopen($filename, "w");
        if (
                @flock($handle, LOCK_EX | LOCK_NB) &&
                @fwrite($handle, $value) &&
                @ flock($handle, LOCK_UN) &&
                @fclose($handle) &&
                is_file($filename) &&
                @filesize($filename) > 0
        ) {
            @chmod($filename, 0777);
            $this->meta[$hash] = [
                'file' => $filename,
                'expire' => $expire,
                'type' => $type
            ];
            return true;
        }
        return false;
    }

    /**
     * Initialize rootdir
     * @param string $root
     * @return bool
     */
    private function initialize(string $root, string $namespace): bool {
        $this->rootpath = $root;
        if (!empty($namespace)) $root .= DIRECTORY_SEPARATOR . $this->getHash($namespace);
        if (($real = realpath($root) ?: null) === null) {
            $path = normalizePath($root);
            $path = preg_match('/^(?:\w\:)?\//', $path) !== 1 ? getcwd() . DIRECTORY_SEPARATOR . $path : $path;
            if (!file_exists($path)) @mkdir($path, 0777, true);
            $real = realpath($path) ?: null;
        }
        if ($real !== null && is_dir($real)) $this->root = $real;
        return $this->root !== null;
    }

    /**
     * Loads the metadata and removes expired files
     * @return void
     */
    private function update(): void {
        $this->meta = [];
        if ($this->root === null) return;

        foreach (listFiles($this->root, "php", "bin") as $file) {
            $base = basename($file);
            $split = explode(".", $base);
            if (count($split) !== 3) continue;
            list($hash, $expire, $type) = $split;
            if (is_numeric($expire) && is_numeric($hash)) {
                $expire = intval($expire);
                if (time() > $expire) @unlink($file);
                else {
                    $this->meta[$hash] = [
                        "file" => $file,
                        "expire" => $expire,
                        "type" => $type
                    ];
                }
            }
        }
    }

    /**
     * Hashes the key
     * @param string $key
     * @return string
     */
    private function getHash(string $key): string {
        return sprintf('%u', crc32($key));
    }

    /**
     * @param string $rootpath Root directory to store the cache
     * @param int|null $ttl default TTL to use to store the files
     */
    public function __construct(string $rootpath, int $ttl = null, string $namespace = "phpcache") {
        parent::__construct($ttl);
        if ($this->initialize($rootpath, $namespace)) $this->update();
    }

}
