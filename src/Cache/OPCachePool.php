<?php

namespace NGSOFT\Tools\Cache;

class OPCachePool extends BasicCachePool {

    /** @var string */
    protected $path;

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

}
