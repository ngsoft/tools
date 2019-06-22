<?php

namespace NGSOFT\Tools\Cache;

use NGSOFT\Tools\Interfaces\CacheAble;
use Serializable;
use SplFileInfo;

class CachedFile extends SplFileInfo {

    public function __construct(string $file_name) {
        parent::__construct($file_name);
    }

    /**
     * Save cache contents
     * @param mixed $contents
     * @return boolean
     */
    public function save($contents) {
        $meta = [
            "type" => gettype($contents),
            "class" => null,
            "contents" => "null"
        ];
        if (in_array($meta["type"], ["unknown type", "resource", "resource (closed)"])) return false;
        if ($meta["type"] === "object") {
            $meta["class"] = get_class($contents);
            if ($contents instanceof CacheAble) {
                $meta["type"] = CacheAble::class;
                $meta["contents"] = var_export($contents->toArray(), true);
            } elseif ($contents instanceof Serializable) {
                $meta["type"] = Serializable::class;
                $meta["contents"] = serialize($contents);
            } else return false;
        } elseif ($meta["type"] === "array") $meta["contents"] = var_export($contents, true);
        else $meta["content"] = $contents;
        //else $meta["content"] = json_encode($contents);
        $tosave = sprintf('<?php return %s;', var_export($meta, true));
        $tmpfile = tempnam($this->getPath(), uniqid("", true));
        return file_put_contents($tmpfile, $tosave, LOCK_EX) !== false ? rename($tmpfile, $this->getPathname()) : false;
    }

    /**
     * Loads Cache Contents
     * @return mixed|null
     */
    public function load() {
        if ($this->isFile()) {
            $content = null;
            ob_start();
            $meta = @include $this->getPath();
            ob_end_clean();
            if (is_array($meta)) {
                if ($meta["class"] !== null) {
                    if ($meta["type"] === Serializable::class) $content = unserialize($meta["contents"]);
                    elseif ($meta["type"] === CacheAble::class) {
                        $content = $meta["class"]::createFromArray($meta["contents"]);
                    }
                } else $content = $meta["contents"];
                return $content;
            }
        }
        return null;
    }

}
