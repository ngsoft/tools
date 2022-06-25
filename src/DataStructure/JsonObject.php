<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\{
    Filesystem\File, Lock\FileSystemLock
};

class JsonObject extends SimpleObject
{

    protected ?File $file = null;
    protected ?FileSystemLock $lock = null;

    public static function fromJsonFile(string $filename, bool $recursive = true): static
    {

        $file = new File($filename);
        $lock = $file->lock();
        try {
            if ( ! $file->exists()) {
                $instance = static::create(recursive: $recursive);
            } else {
                $lock->block(30);
                $instance = static::fromJson($file->read(), $recursive);
            }
            $instance->lock = $lock;
            $instance->file = $file;
            return $instance;
        } finally {
            $lock->release();
        }
    }

    protected function reload(): void
    {
        if ($this->file) {
            $this->lock->block(30);
            if ($this->file->exists() && $this->file->isModified()) {
                $this->storage = $this->file->readJson();
            }
        }
        parent::reload();
    }

    protected function update(): void
    {
        parent::update();
        if ($this->file) {
            $this->file->writeJson($this->storage);
            $this->lock->release();
            var_dump($this->file);
        }
    }

}
