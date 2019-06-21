<?php

namespace NGSOFT\Tools\Cache\OPCache;

class CachedFile extends \SplFileInfo {

    public function __construct(string $file_name) {
        parent::__construct($file_name);
    }

    /**
     * Write into the file
     * @param string $contents
     * @return bool
     */
    public function write(string $contents): bool {
        return file_put_contents($this->getFilename(), $contents, LOCK_EX) !== false;
    }

    /**
     * Reads the file
     * @return string|null
     */
    public function read(): ?string {
        return $this->isFile() ? (file_get_contents($this->getFilename()) ?? null) : null;
    }

}
