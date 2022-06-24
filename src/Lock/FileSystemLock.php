<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Filesystem\File,
    Stringable;

class FileSystemLock extends BaseLockStore
{

    protected File $lockedFile;
    protected File $file;

    public function __construct(
            File $name,
            protected int|float $seconds = 0,
            string|Stringable $owner = '',
            protected bool $autoRelease = true
    )
    {
        parent::__construct($name, $seconds, $owner, $autoRelease);

        $file = $this->file = $name;
        if ($file->extension() === 'lock') {
            $locked = $file;
        } else { $locked = $file->dirname() . DIRECTORY_SEPARATOR . $file->name() . '.lock'; }
    }

    protected function read(): array|false
    {

    }

    protected function write(int|float $until): bool
    {

    }

    public function forceRelease(): void
    {

    }

}
