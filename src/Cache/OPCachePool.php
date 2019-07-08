<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Cache\BasicCacheItem;
use NGSOFT\Tools\Cache\BasicCachePool;
use NGSOFT\Tools\Exceptions\BasicCacheException;
use NGSOFT\Tools\Interfaces\CacheAble;
use Serializable;
use function NGSOFT\Tools\endsWith;
use function NGSOFT\Tools\includeFile;

/**
 * Uses PHP OPCode to store data
 */
class OPCachePool extends BasicCachePool {

    /** @var string */
    private $path;

    /** @var string */
    private $ext = ".opc.php";

    /**
     * Contains correspondances between keys and their expire date
     * @var array<string,int>
     */
    private $meta;

    /** @var string */
    private $metafile = "%s.meta.php";

    /**
     * @param string $path Directory where to store cached items
     * @param ?int $ttl Default time to live for a cached item
     */
    public function __construct(string $path, int $ttl = null) {
        parent::__construct($ttl);
        //normalize path
        $this->path = preg_replace('/[\\\/]+/', '/', $path . DIRECTORY_SEPARATOR);

        $this->metafile = sprintf($this->metafile, basename($this->path));






        $this->loadMetaAndCleanUp();
    }

    ////////////////////////////   Metadatas   ////////////////////////////

    /**
     * Get the saved metadata
     * @return array
     */
    private function loadmeta(): array {
        $filename = $this->path . $this->metafile;
        return $this->opload($filename) ?? [];
    }

    /**
     * Convenient method to update metadatas on disk
     * @return bool
     */
    private function savemeta(): bool {
        $filename = $this->path . $this->metafile;
        return $this->opsave($filename, $this->meta);
    }

    ////////////////////////////   Files Operations   ////////////////////////////

    /**
     * Defines the filename to use to save the cache
     * @param string $key
     * @return string
     */
    private function getFileName(string $key): string {
        return sprintf('%s%u%s', $this->path, crc32($key), $this->ext);
    }

    /**
     * Save opcodes into a file
     * @param string $filename
     * @param mixed $data
     * @throws BasicCacheException
     * @return bool
     */
    private function opsave(string $filename, $data): bool {
        if (in_array(gettype($data), ["unknown type", "resource", "resource (closed)", "NULL"])) return false;
        if (is_dir($filename)) return false;
        $retval = false;
        $dir = dirname($filename);
        $tmp = @tempnam($dir, basename($filename));

        if (is_dir($dir)) {
            if (is_object($data)) {
                if ($data instanceof \Serializable) $value = '<?php return unserialize(' . serialize($value) . ');';
                elseif ($data instanceof CacheAble) {
                    $value = '<?php return '
                            . get_class($data) . '::__set_state('
                            . var_export($data->toArray(), true) . ');';
                }
                return false;
            } else $value = '<?php return ' . var_export($value, true) . ';';
            set_time_limit(60);
            $old = umask(0);
            file_exists($dir) || @mkdir($filename);
            if (!is_dir($dir)) throw new BasicCacheException(sprintf('Cannot use "%s" as Cache location (not a dir).', $dir));
            is_file($filename) && @unlink($filename);
            umask(022);
            if (@file_put_contents($tmp, $value, LOCK_EX)) {
                usleep(200000);
                $i = 0;
                do {
                    //rename seems to not run well on big files
                    if (($retval = @rename($tmp, $filename))) break;
                    if ($i === 5) break;
                    usleep(400000);
                    ++$i;
                }while (true);
            }
            umask($old);
        }

        return $retval;
    }

    /**
     * Loads opcodes
     * @param string $filename
     * @return mixed|false
     */
    private function opload(string $filename) {
        return includeFile($filename);
    }

    ////////////////////////////   OPCache Methods   ////////////////////////////




    protected function doContains(string $key): bool {

    }

    protected function doDelete(string $key): bool {

    }

    protected function doFetch(string $key): BasicCacheItem {

    }

    protected function doFlush(): bool {

    }

    protected function doSave(BasicCacheItem $item): bool {

    }

    /** {@inheritdoc} */
    protected function clearCache(): bool {
        $success = true;
        foreach (scandir($this->path) as $file) {
            if (endsWith($this->ext, $file)) {
                if (@unlink($this->path . DIRECTORY_SEPARATOR . $file) !== true) $success = false;
            }
        }
        $this->meta = [];
        $this->savemeta();
        return $success;
    }

    /** {@inheritdoc} */
    protected function deleteCache(string $key): bool {
        $fn = $this->getFileName($key);
        if (!file_exists($fn)) return true;
        unset($this->meta[$key]);
        return @unlink($fn);
    }

    /** {@inheritdoc} */
    protected function hasCache(string $key): bool {
        $filename = $this->getFileName($key);
        $expire = $this->meta[$key] ?? 0;
        return file_exists($filename) && $expire > time();
    }

    /** {@inheritdoc} */
    protected function writeCache(BasicCacheItem $item): bool {
        $key = $item->getKey();
        $this->deleteCache($key); //clears current value
        $expire = $item->getExpireAt()->getTimestamp();
        if (time() > $expire) return false;

        $value = $item->getRawValue();
        if (in_array(gettype($value), ["unknown type", "resource", "resource (closed)", "null"])) return false;
        if ($value instanceof CacheAble) {
            $tosave = '<?php return ' . var_export($value, true) . ';';
        } elseif ($value instanceof Serializable) {
            $tosave = '<?php return unserialize(' . serialize($value) . ');';
        } elseif (is_object($value)) return false;
        else $tosave = '<?php return ' . var_export($value, true) . ';';
        $this->meta[$key] = $expire;
        $this->savemeta();
        $tmp = tempnam($this->path, $key);
        $file = $this->getFileName($key);
        if (file_put_contents($tmp, $tosave, LOCK_EX)) {
            return rename($tmp, $file);
        }
        return false;
    }

    /** {@inheritdoc} */
    protected function readCache(string $key): BasicCacheItem {
        if (!$this->hasCache($key)) return $this->createEmptyItem($key);
        $value = includeFile($this->getFileName($key));
        return new BasicCacheItem($key, $this->ttl, true, $value);
    }

}
