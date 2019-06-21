<?php

namespace NGSOFT\Tools\Cache\OPCache;

use NGSOFT\Tools\Cache\BasicCachePool;
use NGSOFT\Tools\Exceptions\BasicCacheException;
use Psr\Cache\CacheItemInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;
use function NGSOFT\Tools\rrmdir;

/**
 * Uses PHP OPCode to store data
 */
class OPCachePool extends BasicCachePool {

    /** @var string */
    private $path;

    /** @var CacheMap */
    private $map;

    /** @var CachedFile  */
    private $mapFile;

    /** @var string */
    private $ext = ".opcache";

    /**  @return string */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * @param type $path
     * @return static
     */
    public function setPath($path): self {
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
        } catch (RuntimeException $exc) {
            echo $this->log($exc->getMessage());
            throw $exc;
        }
        return true;
    }

    private function loadMap() {
        $filename = $this->path . DIRECTORY_SEPARATOR . md5($this->path) . ".index" . $this->ext;
        $cf = $this->mapFile = new CachedFile($filename);
        if (($data = $this->loadFile($cf))) {
            $this->map = CacheMap::____set_state($cf["contents"]);
        } else $this->map = new CacheMap();
    }

    private function saveMap(): bool {
        return $this->saveFile($this->mapFile, $this->map->toArray());
    }

    private function loadFile(CachedFile $cf): ?array {
        ob_start();
        if ($cf->isFile()) @include($cf->getFilename());
        ob_end_clean();
        return $var ?? null;
    }

    private function saveFile(CachedFile $cf, array $data): bool {
        $string = sprintf('<?php $val = %s;', var_export($array, true));
        $filename = $cf->getFilename();
        $fn = new CachedFile(sprintf("%s.tmp", $filename));
        if ($fn->write($string)) return rename($fn->getFilename(), $filename);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function clearCache(): bool {
        if (rrmdir($this->path) and $this->makePath()) {
            $this->map = new CacheMap();
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function createCache(string $key): CacheItemInterface {

    }

    /**
     * {@inheritdoc}
     */
    protected function deleteCache($keys): bool {

    }

    /**
     * {@inheritdoc}
     */
    public function getCache(CacheItemInterface $item) {

    }

    /**
     * {@inheritdoc}
     */
    protected function hasCache(string $key): bool {

    }

    /**
     * {@inheritdoc}
     */
    protected function writeCache($items): bool {

    }

}
