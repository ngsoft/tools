<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Tools,
    Throwable;

class FileStore extends BaseLockStore
{

    public function __construct(
            string $name,
            int|float $seconds,
            string $owner = '',
            protected string $rootpath = '',
            protected string $prefix = '@lockstore'
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

    protected function read(): array
    {

        try {
            Tools::errors_as_exceptions();
            return require $this->getFilename();
        } catch (Throwable) {
            return [0, ''];
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

        $contents = sprintf(
                "<?php\nreturn [%u => %f, %u => %s];",
                static::KEY_UNTIL, $this->seconds + $this->timestamp(),
                static::KEY_OWNER, var_export($this->owner(), true)
        );

        while ($retry < 3) {

            try {

                Tools::errors_as_exceptions();

                if (is_dir($dirname) || mkdir($dirname, 0777, true)) {
                    return file_put_contents($filename, $contents) !== false;
                }
            } catch (Throwable $error) {
                usleep((100 + random_int(-10, 10)) * 1000);
            } finally { \restore_error_handler(); }
            $retry ++;
        }

        return false;
    }

    protected function isOwner(string $currentOwner): bool
    {
        return $this->read()[1] === $currentOwner;
    }

    public function acquire(bool $blocking = false): bool
    {


        if ($lock = $this->read()) {

        }
    }

    public function forceRelease(): void
    {

    }

    public function getRemainingLifetime(): float|int
    {

    }

    public function isAcquired(): bool
    {

    }

    public function release(): bool
    {

    }

}
