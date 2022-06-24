<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Filesystem\File,
    Stringable;

class FileSystemLock extends BaseLockStore
{

    protected File $lockedFile;
    protected File $file;

    /**
     * @param string|File $name
     * @param int|float $seconds
     * @param string $owner
     * @param bool $autoRelease
     */
    public function __construct(
            string|File $name,
            protected int|float $seconds = 0,
            string|Stringable $owner = '',
            protected bool $autoRelease = true
    )
    {
        parent::__construct($name, $seconds, $owner, $autoRelease);

        if ( ! $name instanceof File) {
            $name = new File($name);
        }

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
