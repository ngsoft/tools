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

    /** @var array<string,int> */
    private $meta = [];

    /** @var string|null */
    private $root;

    /** {@inheritdoc} */
    protected function doContains(string $key): bool {
        $this->update();
        return isset($this->meta[$this->getHash($key)]);
    }

    /** {@inheritdoc} */
    protected function doDelete(string $key): bool {
        $hash = $this->getHash($key);
        if (isset($this->meta[$hash])) {
            $file = $this->meta[$hash];
            unset($this->meta[$hash]);
            return @unlink($this->meta[$hash]);
        }
        return true;
    }

    /** {@inheritdoc} */
    protected function doFetch(string $key): CacheItem {
        if (!$this->doContains($key)) return $this->createEmptyItem($key);
        $hash = $this->getHash($key);
        $file = $this->meta[$hash];
        $contents = safeInclude($file);
        return new CacheItem($key, $this->getTTL(), $contents !== null, $contents);
    }

    /** {@inheritdoc} */
    protected function doFlush(): bool {
        if ($this->root !== null && rrmdir($this->root)) return @mkdir($this->root, 0777, true);
        return false;
    }

    /** {@inheritdoc} */
    protected function doSave(CacheItem $item): bool {
        if ($this->root === null) return false;
        $expire = $item->getExpireAt()->getTimestamp();
        $key = $item->getKey();
        $hash = $this->getHash($key);
        $contents = $item->get();
        $filename = $this->root . sprintf(self::FILE_PATTERN, $hash, $expire, self::FILE_EXTENSION);
        $this->write($filename, $contents);
        return $this->doContains($key);
    }

    private function write(string $filename, $contents) {

    }

    /**
     * Initialize rootdir
     * @param string $root
     * @return void
     */
    private function initialize(string $root): void {
        $root .= "/phpcache";
        if (($real = realpath($root) ?: null) === null) {
            $path = normalizePath($root);
            $path = preg_match('/^(?:\w\:)?\//', $path) !== 1 ? getcwd() . DIRECTORY_SEPARATOR . $path : $path;
            if (!file_exists($path)) @mkdir($path, 0777, true);
            $real = realpath($path) ?: null;
        }
        if (is_dir($real)) $this->root = $real;
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
                else $this->meta[$hash] = $file;
            }
        }
    }

    /**
     * Hashes the key
     * @param string $key
     * @return string
     */
    private function getHash(string $key): string {
        return md5($key);
    }

    /**
     * @param string $rootpath Root directory to store the cache
     * @param int $ttl default TTL to use to store the files
     */
    public function __construct(string $rootpath, int $ttl = null) {
        parent::__construct($ttl);
        $this->initialize($rootpath);
    }

}
