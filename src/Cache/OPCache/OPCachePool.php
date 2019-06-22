<?php

namespace NGSOFT\Tools\Cache\OPCache;

use NGSOFT\Tools\Cache\BasicCacheItem;
use NGSOFT\Tools\Cache\BasicCachePool;
use NGSOFT\Tools\Exceptions\BasicCacheException;
use function NGSOFT\Tools\endsWith;

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

    /** @var CachedFile */
    private $metaCache;

    /**
     * @param string $path Directory where to store cached items
     * @param ?int $ttl Default time to live for a cached item
     */
    public function __construct(string $path, int $ttl = null) {
        parent::__construct($ttl);
        //make the path
        if (!file_exists($path)) @mkdir($path, 0666, true);
        if (!is_dir($path)) throw new BasicCacheException(sprintf('Cannot use "%s" as Cache location (not a dir).', $path));
        $this->loadMetaAndCleanUp();
    }

    ////////////////////////////   Metadatas   ////////////////////////////

    /**
     * Loads the cached metadatas, remove entries that are expired and update the meta cache
     */
    private function loadMetaAndCleanUp() {
        $filename = $this->path . DIRECTORY_SEPARATOR . sprintf('%s.index.%s', md5($this->path), $this->ext);
        $this->metaCache = new CachedFile($filename);
        $this->meta = $this->metaCache->load() ?? [];
        $meta = array_merge([], $this->meta);
        $ct = time();
        foreach ($meta as $key => $expire) {
            if ($ct > $expire) $this->deleteCache($key);
        }
        $this->metaCache->save($this->meta);
    }

    private function getFileName(string $key): string {
        return sprintf('%s/%s%s', $this->path, md5($key), $this->ext);
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
        if (time() > $expire) return false; //that can happen
        $cf = new CachedFile($this->getFileName($item->getKey()));
        //update meta
        if ($cf->save($item->getRawValue())) {
            $this->meta[$key] = $expire;
            $this->metaCache->save($this->meta);
            return true;
        }
        return false;
    }

    protected function readCache(string $key): BasicCacheItem {
        if (!$this->hasCache($key)) return $this->createEmptyItem($key);
        $cf = new CachedFile($this->getFileName($key));
        $contents = $cf->load();
        return new BasicCacheItem($key, $this->ttl, true, $contents);
    }

}
