<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Cache\BasicCacheItem;
use NGSOFT\Tools\Cache\BasicCachePool;
use NGSOFT\Tools\Exceptions\BasicCacheException;
use function NGSOFT\Tools\endsWith;
use function NGSOFT\Tools\includeFile;

/**
 * Uses PHP OPCode to store data
 */
class OPCachePool extends BasicCachePool {

    /** @var string */
    private $path;

    /** @var string */
    private $ext = ".opcache.php";

    /**
     * Contains correspondances between keys and their expire date
     * @var array<string,int>
     */
    private $meta;

    /** @var string */
    private $metafile;

    /**
     * @param string $path Directory where to store cached items
     * @param ?int $ttl Default time to live for a cached item
     */
    public function __construct(string $path, int $ttl = null) {
        parent::__construct($ttl);
        $this->path = $path;
        //make the path
        if (!file_exists($path)) @mkdir($path, 0666, true);
        if (!is_dir($path)) {
            throw new BasicCacheException(sprintf('Cannot use "%s" as Cache location (not a dir).', $path));
        }
        $this->loadMetaAndCleanUp();
    }

    ////////////////////////////   Metadatas   ////////////////////////////

    /**
     * Loads the cached metadatas, remove entries that are expired and update the meta cache
     */
    private function loadMetaAndCleanUp() {

        //sprintf('%u', crc32($this->icon));

        $this->metafile = sprintf('/%s.db.php', basename($path));

        loadMeta();
        $ct = time();
        $meta = $this->meta;
        $c = 0;
        foreach ($meta as $key => $expire) {
            if ($ct > $expire) {
                $this->deleteCache((string) $key);
                unset($this->meta[$key]);
                $c++;
            }
        }
        if ($c > 0) $this->savemeta();
    }

    private function loadMeta() {
        $meta = includeFile($this->path . $this->metafile);
        if (is_array($meta)) $this->meta = $meta;
        else $this->meta = [];
    }

    private function savemeta(): bool {
        $tosave = sprintf('<?php return %s;', var_export($this->meta, true));
        $tmp = tempnam($this->path, basename($this->path));
        if (file_put_contents($tmp, $tosave, LOCK_EX)) {
            return rename($tmp, $this->path . $this->metafile);
        }
        return false;
    }

    private function getFileName(string $key): string {
        return sprintf('%s/%u%s', $this->path, crc32($key), $this->ext);
    }

    ////////////////////////////   OPCache Methods   ////////////////////////////

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
        if (in_array(gettype($value), ["unknown type", "resource", "resource (closed)"])) return false;
        if ($value instanceof \Serializable) {
            $tosave = '<?php return unserialize(' . serialize($value) . ');';
        } elseif ($value instanceof \NGSOFT\Tools\Interfaces\CacheAble) {
            $tosave = '<?php return ' . var_export($value, true) . ';';
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

    protected function readCache(string $key): BasicCacheItem {
        if (!$this->hasCache($key)) return $this->createEmptyItem($key);
        $value = includeFile($this->getFileName($key));
        return new BasicCacheItem($key, $this->ttl, true, $value);
    }

}
