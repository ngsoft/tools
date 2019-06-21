<?php

namespace NGSOFT\Tools\Cache\OPCache;

use NGSOFT\Tools\Cache\BasicCachePool;
use Psr\Cache\CacheItemInterface;
use Psr\Container\ContainerInterface;

/**
 * Uses PHP OPCode to store data
 */
class OPCachePool extends BasicCachePool {

    /** @var string */
    private $path;

    /**
     * @var CacheMap
     */
    private $map;

    /** @var CachedFile  */
    private $mapFile;

    /** @var string */
    private $ext = ".opcache";

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    /**
     * @param string $path The directory where to store the cache
     * @param int $ttl Default TTL for a cache item
     * @param ContainerInterface $container
     */
    public function __construct(string $path, int $ttl = null, ContainerInterface $container = null) {
        parent::__construct($container, $ttl);
        $this->setPath($path);
        $this->makePath();
        $this->loadMap();
    }

    private function makePath() {
        try {
            if (!file_exists($this->path)) @mkdir($this->path, 0666, true);
            if (!is_dir($this->path)) {
                throw new BasicCacheException(sprintf("Cannot use %s as cache path.", $this->path));
            }
        } catch (\RuntimeException $exc) {
            echo $this->log($exc->getMessage());
            throw $exc;
        }
    }

    private function loadMap() {
        $filename = $this->path . DIRECTORY_SEPARATOR . md5($this->path) . ".index.opcache";
        $cf = $this->mapFile = new CachedFile($filename);
        if (($data = $this->loadFile($cf))) {
            $this->map = CacheMap::____set_state($cf["contents"]);
        }
    }

    private function saveMap() {
        $this->saveFile($this->mapFile, $this->map->toArray());
    }

    private function loadFile(CachedFile $cf): ?array {
        ob_start();
        if ($cf->isFile()) @include($cf->getFilename());
        ob_end_clean();
        return $var ?? null;
    }

    private function saveFile(CachedFile $cf, array $data) {
        $string = sprintf('<?php $val = %s;', var_export($array, true));
        $filename = $cf->getFilename();
        $fn = new CachedFile(sprintf("%s.tmp", $filename));
        if ($fn->write($string)) return rename($fn->getFilename(), $filename);
        return false;
    }

    protected function clearCache(): bool {

    }

    protected function createCache(string $key): CacheItemInterface {

    }

    protected function deleteCache($keys): bool {

    }

    public function getCache(CacheItemInterface $item) {

    }

    protected function hasCache(string $key): bool {

    }

    protected function writeCache($items): bool {

    }

}
