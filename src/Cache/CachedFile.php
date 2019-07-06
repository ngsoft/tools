<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Interfaces\CacheAble;
use Serializable;
use SplFileInfo;

class CachedFile extends SplFileInfo {

    /**
     * Save cache contents
     * @param mixed $contents
     * @return boolean
     */
    public function save($contents) {
        $meta = [
            "type" => gettype($contents),
            "class" => null,
            "contents" => null
        ];
        if (in_array($meta["type"], ["unknown type", "resource", "resource (closed)"])) return false;
        if ($meta["type"] === "object") {
            $meta["class"] = get_class($contents);
            if ($contents instanceof CacheAble) {
                $meta["type"] = CacheAble::class;
                $meta["contents"] = $contents->toArray();
            } elseif ($contents instanceof Serializable) {
                $meta["type"] = Serializable::class;
                $meta["contents"] = serialize($contents);
            } else return false;
        } else $meta["contents"] = $contents;
        $tosave = sprintf('<?php return %s;', var_export($meta, true));
        $tmpfile = tempnam($this->getPath(), uniqid("", true));
        chmod($tmpfile, 0666);
        if (file_put_contents($tmpfile, $tosave) !== false) {
            @unlink($this->getPathname());
            return rename($tmpfile, $this->getPathname());
        }
        return false;
    }

    /**
     * Loads Cache Contents
     * @return mixed|null
     */
    public function load() {
        $content = null;
        if ($this->isFile()) {
            ob_start();
            $meta = @include $this->getPathname();
            ob_end_clean();
            if (is_array($meta)) {
                $content = $meta["contents"];
                if ($meta["class"] !== null) {
                    if (($meta["type"] === Serializable::class) and is_string($content)) $content = unserialize($content);
                    elseif (($meta["type"] === CacheAble::class) and is_array($content)) {
                        $content = $meta["class"]::__set_state($meta["contents"]);
                    } else return null;
                }
            }
        }

        return $content;
    }

}
