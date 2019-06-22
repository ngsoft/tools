<?php

namespace NGSOFT\Tools\Cache\OPCache;

use NGSOFT\Tools\Cache\BasicCachePool;

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
    }

    ////////////////////////////   OPCache Methods   ////////////////////////////

    protected function clearCache(): bool {

    }

    protected function deleteCache($keys): bool {

    }

    protected function hasCache(string $key): bool {

    }

    protected function loadCache(string $key): \NGSOFT\Tools\Cache\BasicCacheItem {

    }

    protected function writeCache(\NGSOFT\Tools\Cache\BasicCacheItem $item): bool {

    }

}
