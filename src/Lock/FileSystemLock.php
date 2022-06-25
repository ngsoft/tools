<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\{
    Filesystem\File, Tools
};
use Stringable;

/**
 * Creates a lock file with the same filename and directory as provided file
 */
class FileSystemLock extends BaseLockStore
{

    protected File $file;

    public function __construct(
            File $name,
            protected int|float $seconds = 0,
            string|Stringable $owner = '',
            protected bool $autoRelease = true
    )
    {
        parent::__construct($name, $seconds, $owner, $autoRelease);

        if ($name->extension() === 'lock') {
            $locked = $name->getPath();
        } else { $locked = $name->dirname() . DIRECTORY_SEPARATOR . $name->name() . '.lock'; }

        $this->file = new File($locked);
    }

    protected function read(): array|false
    {
        $data = $this->file->require();
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
            return $this->file->write($contents);
        } finally {
            restore_error_handler();
        }
    }

    public function forceRelease(): void
    {
        if ($this->file->unlink()) {
            $this->until = 1;
        }
    }

}
