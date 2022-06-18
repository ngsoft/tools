<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Tools,
    Throwable;
use function NGSOFT\Tools\safe;

class FileStore extends BaseLockStore
{

    public function __construct(
            string $name,
            int|float $seconds,
            string $owner = '',
            protected bool $autoRelease = true,
            protected string $rootpath = '',
            protected string $prefix = '@flocks'
    )
    {
        parent::__construct($name, $seconds, $owner);

        if (empty($rootpath)) {
            $rootpath = $this->rootpath = sys_get_temp_dir();
        }

        try {

        } catch (\Throwable) {

        }
    }

    protected function getFilename(): string
    {
        return $this->rootpath . DIRECTORY_SEPARATOR . $this->prefix . hash('MD5', $this->name) . '.lock';
    }

    protected function read(): array|false
    {

        try {
            Tools::errors_as_exceptions();
            return require $this->getFilename();
        } catch (Throwable) {
            return false;
        } finally { restore_error_handler(); }
    }

    protected function write(): bool
    {
        if (is_file($this->getFilename())) {
            return false;
        }

        $retry = 0;

        $filename = $this->getFilename();

        $dirname = dirname($filename);

        loop:

        if ($retry === 3) {
            return false;
        }
        try {

            Tools::errors_as_exceptions();

            $until = $this->seconds + $this->timestamp();

            $contents = sprintf(
                    "<?php\nreturn [%u => %f, %u => %s];",
                    static::KEY_UNTIL, $until,
                    static::KEY_OWNER, var_export($this->owner(), true),
            );

            if (is_dir($dirname) || mkdir($dirname, 0777, true)) {
                if (file_put_contents($filename, $contents) !== false) {

                    $this->acquired = true;
                    $this->until = $until;
                }
            }
        } catch (Throwable $error) {
            $this->waitFor();
            $retry ++;
            goto loop;
        } finally { \restore_error_handler(); }
    }

    protected function isOwner(string $currentOwner): bool
    {
        return $this->read()[self::KEY_OWNER] === $currentOwner;
    }

    public function acquire(): bool
    {

        if ( ! $this->isExpired($this->until)) {
            return true;
        }

        $canAcquire = false;
        if ($lock = $this->read()) {
            if ($this->isExpired($lock[self::KEY_UNTIL])) {
                $canAcquire = true;
            } elseif ($this->owner === $lock[self::KEY_OWNER]) {
                return true;
            }
        } else { $canAcquire = true; }


        return $canAcquire ? $this->write() : false;
    }

    public function forceRelease(): void
    {
        safe('unlink', $this->getFilename());
    }

    public function release(): bool
    {

        if (
                $this->isAcquired() &&
                safe('unlink', $this->getFilename())
        ) {
            $this->until = 0;
            return true;
        }

        return false;
    }

}
