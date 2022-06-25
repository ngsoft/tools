<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\{
    Filesystem\File, Lock\FileSystemLock
};
use ValueError;
use function get_debug_type,
             wait;

/**
 * A Json object that syncs data with a json file concurently
 */
class JsonObject extends SimpleObject
{

    protected ?File $file = null;
    protected ?FileSystemLock $lock = null;

    public static function fromJsonFile(string $filename, bool $recursive = true): static
    {

        $file = new File($filename);
        $lock = $file->lock(30);
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
                $retry = 0;

                while ($retry < 3) {
                    if ($data = $this->file->readJson()) {
                        break;
                    }
                    wait();
                    $retry ++;
                }

                if ( ! is_array($data)) {
                    throw new ValueError(sprintf('Invalid json return type, array expected, %s given.', get_debug_type($data)));
                }


                $this->storage = $data;
            }
        }
        parent::reload();
    }

    protected function update(): void
    {
        parent::update();
        if ($this->file) {

            $retry = 0;
            while ($retry < 3) {
                if ($this->file->writeJson($this->storage)) {
                    break;
                }
                wait();
                $retry ++;
            }
            $this->lock->release();
        }
    }

    /**
     * Checks if value is valid
     */
    protected function assertValidValue(mixed $value): void
    {
        // accepts anything, override this to set your conditions
        if ( ! is_scalar($value) && ! is_array($value) && ! is_null($value)) {

            throw new ValueError(sprintf('%s can only use types string|int|float|bool|null|array|\\%s, %s given.', $this, Collection::class, get_debug_type($value)));
        }
    }

}
