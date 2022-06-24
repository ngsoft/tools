<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\{
    Filesystem\File, Tools
};
use Stringable;

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
        $data = $this->lockedFile->require();
        return is_array($data) ? $data : false;
    }

    protected function write(int|float $until): bool
    {

        $contents = sprintf(
                "<?php\nreturn [%u => %f, %u => %s];",
                static::KEY_UNTIL, $until,
                static::KEY_OWNER, var_export($this->getOwner(), true),
        );

        try {
            Tools::errors_as_exceptions();
            return $this->lockedFile->write($contents);
        } finally {
            restore_error_handler();
        }
    }

    public function forceRelease(): void
    {
        $this->lockedFile->unlink();
    }

}
