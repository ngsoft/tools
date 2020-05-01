<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Cache\{
    CacheItem, CachePool
};
use Serializable;
use function NGSOFT\Tools\{
    endsWith, includeFile, safe_exec
};

/**
 * Uses PHP OPCode to store data
 */
class OPCachePool extends CachePool {

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
    private $metafile;

    /**
     * @param string $path Directory where to store cached items
     * @param ?int $ttl Default time to live for a cached item
     */
    public function __construct(string $path, int $ttl = null) {
        parent::__construct($ttl);
        //normalize path
        $this->path = preg_replace('#[\\\/]+#', '/', $path . DIRECTORY_SEPARATOR);
        $this->metafile = sprintf("%s.meta.php", basename($this->path));
        //loads meta from disk
        $this->meta = $this->loadmeta();
        //clean up expired items and cache miss
        $this->doClean();
    }

    ////////////////////////////   Metadatas   ////////////////////////////

    /**
     * Get the saved metadata
     * @return array
     */
    private function loadmeta(): array {
        $filename = $this->path . $this->metafile;
        return $this->opload($filename) ?: [];
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
     * @suppress PhanRedundantCondition
     * @param string $filename
     * @param mixed $data
     * @return bool
     */
    private function opsaveOld(string $filename, $data): bool {
        if (in_array(gettype($data), ["unknown type", "resource", "resource (closed)", "NULL"])) return false;
        if (is_dir($filename)) return false;
        $retval = false;
        $dir = dirname($filename);
        $tmp = @tempnam($dir, basename($filename));

        // Set File Contents
        if (is_object($data)) {
            if ($data instanceof Serializable) $value = '<?php return unserialize(\'' . serialize($data) . '\');';
            elseif (method_exists($data, '__set_state')) {
                if ($data instanceof \JsonSerializable) $array = $data->jsonSerialize();
                elseif (method_exists($data, 'toArray')) $array = $data->toArray();
                if (!isset($array)) return false;;
                $value = '<?php return '
                        . get_class($data) . '::__set_state('
                        . var_export($array, true) . ');';
            } else return false;
        } else $value = '<?php return ' . var_export($data, true) . ';';

        // Write
        set_time_limit(60);
        file_exists($dir) || @mkdir($dir, 0777, true);
        if (!is_dir($dir)) return false;
        is_file($filename) && @unlink($filename);
        if (@file_put_contents($tmp, $value, LOCK_EX)) {
            do {
                //rename seems to not run well on big files
                if (($retval = @rename($tmp, $filename))) {
                    // cli mode can have a different user from http mode
                    chmod($filename, 0777);
                    break;
                }
                if (!isset($i)) $i = 1;
                if ($i == 5) break;
                ++$i;
                // wait for lock to be removed (big files)
                usleep(2000);
            }while (true);
        }
        return $retval;
    }

    /**
     * Loads opcodes
     * @param string $filename
     * @return mixed|false
     */
    private function oploadOld(string $filename) {
        return safe_exec(function () use($filename) {
            return includeFile($filename);
        });
    }

    ////////////////////////////   OPCache Methods   ////////////////////////////

    /**
     * Cleans up cache path from expired items
     * And cache miss
     */
    protected function doClean() {
        $path = realpath($this->path);
        if ($path and is_dir($path)) {
            foreach (scandir($path) as $file) {
                if (endsWith($file, ".tmp")) @unlink($path . DIRECTORY_SEPARATOR . $file);
            }
        }

        $now = time();
        foreach ($this->meta as $key => $expire) {
            if ($now > $expire) $this->doDelete($key);
        }
    }

    /** {@inheritdoc} */
    protected function doContains(string $key): bool {
        $expire = $this->meta[$key] ?? 0;
        if (time() > $expire) return false;
        return is_file($this->getFileName($key));
    }

    /** {@inheritdoc} */
    protected function doDelete(string $key): bool {
        // do not clear the expiration to prevent a file write
        // as doContains checks for the file presence
        $filename = $this->getFileName($key);
        if (is_file($filename)) return @unlink($filename);
        // file does not exists so we can say it has been removed
        return true;
    }

    /** {@inheritdoc} */
    protected function doFetch(string $key): CacheItem {
        if (!$this->doContains($key)) return $this->createEmptyItem($key);
        return new CacheItem($key, $this->getTTL(), true, $this->opload($this->getFileName($key)));
    }

    /** {@inheritdoc} */
    protected function doFlush(): bool {
        $path = realpath($this->path);
        if ($path and is_dir($path)) {
            foreach (scandir($path) as $file) {
                if (
                        (endsWith($file, $this->ext))
                        //tmpfiles created by tempnam()
                        or ( endsWith($file, ".tmp"))
                ) {
                    @unlink($path . DIRECTORY_SEPARATOR . $file);
                }
            }
            //clears meta and sync
            $this->meta = [];
            $this->savemeta();
        }
        return true;
    }

    /** {@inheritdoc} */
    protected function doSave(CacheItem $item): bool {
        $expire = $item->getExpireAt()->getTimestamp();
        if (time() > $expire) return false;
        $key = $item->getKey();
        $value = $item->getRawValue();
        if (($retval = $this->opsave($this->getFileName($key), $value))) {
            $this->meta[$key] = $expire;
            $this->savemeta();
        }
        return $retval;
    }

}
