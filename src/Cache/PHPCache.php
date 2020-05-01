<?php

namespace NGSOFT\Tools\Cache;

use function NGSOFT\Tools\{
    listFiles, normalizePath, rrmdir, safeInclude
};

/**
 * Caches data using var_export
 */
final class PHPCache extends CachePool {

    const FILE_TEMPLATE = '<?php return %s;';
    const FILE_PATTERN = '/%s.%d.%s';
    const FILE_EXTENSION = 'cache.php';

    /** @var array<string,array> */
    private $meta = [];

    /** @var string|null */
    private $root;

    /** {@inheritdoc} */
    protected function doContains(string $key): bool {
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
        $contents = safeInclude($file);
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
        $filename = $this->root . sprintf(self::FILE_PATTERN, $hash, $expire, self::FILE_EXTENSION);
        if ($this->doWrite($filename, $contents)) {
            $this->meta[$hash] = [
                'file' => $filename,
                'expire' => $expire
            ];
        }
        return $this->doContains($key);
    }

    /**
     * Compile the cached file
     * @param string $filename
     * @param mixed $data
     * @return bool
     */
    private function doWrite(string $filename, $data): bool {
        if (in_array(gettype($data), ["unknown type", "resource", "resource (closed)", "NULL"])) return false;
        if (is_object($data)) {
            if ($data instanceof Serializable) $value = sprintf("unserialize ('%s')", serialize($data));
            elseif (method_exists($data, '__set_state')) {
                if ($data instanceof JsonSerializable) $array = $data->jsonSerialize();
                elseif (method_exists($data, 'toArray')) $array = $data->toArray();
                if (!isset($array)) return false;
                $value = sprintf('%s::__set_state(%s)', get_class($data), var_export($data, true));
            } else return false;
        } else $value = var_export($data, true);
        $value = sprintf(self::FILE_TEMPLATE, $value);
        //save file
        set_time_limit(120);
        $handle = fopen($filename, "w");
        $return = fwrite($handle, $value) !== false;
        if (fclose($handle)) chmod($filename, 0777);
        return $return;
    }

    /**
     * Initialize rootdir
     * @param string $root
     * @return bool
     */
    private function initialize(string $root): bool {
        $root .= "/phpcache";
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

        foreach (listFiles($this->root, self::FILE_EXTENSION) as $file) {
            $base = basename($file);
            $split = explode(".", $base);
            list($hash, $expire) = $split;
            if (is_numeric($expire)) {
                $expire = intval($expire);
                if (time() > $expire) @unlink($file);
                else {
                    $this->meta[$hash] = [
                        "file" => $file,
                        "expire" => $expire
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
        return crc32($key);
    }

    /**
     * @param string $rootpath Root directory to store the cache
     * @param int|null $ttl default TTL to use to store the files
     */
    public function __construct(string $rootpath, int $ttl = null) {
        parent::__construct($ttl);
        if ($this->initialize($rootpath)) $this->update();
    }

}
