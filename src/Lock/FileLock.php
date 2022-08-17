<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use InvalidArgumentException,
    Stringable,
    Throwable;
use function NGSOFT\Tools\safe,
             set_default_error_handler;

/**
 * Uses php files to create locks
 */
class FileLock extends BaseLockStore
{

    /**
     * @param string|Stringable $name
     * @param int|float $seconds
     * @param string|Stringable $owner
     * @param bool $autoRelease
     * @param string $rootpath where to put the locks
     * @param string $prefix subdirectory to $rootpath
     * @throws InvalidArgumentException
     */
    public function __construct(
            string|Stringable $name,
            protected int|float $seconds = 0,
            string|Stringable $owner = '',
            protected bool $autoRelease = true,
            protected string $rootpath = '',
            protected string $prefix = '@flocks'
    )
    {
        parent::__construct($name, $seconds, $owner, $autoRelease);

        if (empty($rootpath)) {
            $rootpath = $this->rootpath = sys_get_temp_dir();
        }

        if ( ! safe(fn($root) => is_dir($root) || mkdir($root), $rootpath)) {
            throw new InvalidArgumentException(sprintf('%s does not exits and cannot be created.', $rootpath));
        }
        if ( ! is_writable($rootpath)) {
            throw new InvalidArgumentException(sprintf('%s is not writable.', $rootpath));
        }
    }

    protected function getFilename(): string
    {
        return $this->rootpath . DIRECTORY_SEPARATOR . $this->prefix . DIRECTORY_SEPARATOR . $this->getHashedName() . '.lock';
    }

    protected function read(): array|false
    {

        try {
            set_default_error_handler();
            return require $this->getFilename();
        } catch (Throwable) {
            return false;
        } finally { restore_error_handler(); }
    }

    protected function write(int|float $until): bool
    {
        $filename = $this->getFilename();
        $dirname = dirname($filename);
        try {

            set_default_error_handler();
            $contents = sprintf(
                    "<?php\nreturn [%u => %f, %u => %s];",
                    static::KEY_UNTIL, $until,
                    static::KEY_OWNER, var_export($this->getOwner(), true),
            );

            if (is_dir($dirname) || mkdir($dirname, 0777, true)) {
                return file_put_contents($filename, $contents) !== false;
            }
        } catch (Throwable $error) {

        } finally { \restore_error_handler(); }


        return false;
    }

    /** {@inheritdoc} */
    public function forceRelease(): void
    {
        $filename = $this->getFilename();
        if (is_file($filename) && safe('unlink', $filename)) {
            $this->until = 1;
        }
    }

}
