<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Interfaces\LockStore;
use function blank,
             random_string;

abstract class BaseLockStore implements LockStore
{

    protected const KEY_UNTIL = 0;
    protected const KEY_OWNER = 1;
    protected const KEY_PID = 2;

    protected int $pid;

    public function __construct(
            public readonly string $name,
            protected int|float $seconds,
            protected string $owner = '',
            protected bool $autoRelease = true
    )
    {
        $this->pid = getmypid();
        $this->owner = empty($owner) ?
                random_string() :
                $owner;
    }

    /** {@inheritdoc} */
    public function block(int|float $seconds, mixed $callback = null): mixed
    {

        $starting = $this->timestamp();
        while ( ! $this->acquire()) {
            usleep((100 + random_int(-10, 10)) * 1e+3);

            if ($this->timestamp() - $seconds >= $starting) {
                throw new LockTimeout(sprintf('Lock %s timeout.', $this->name));
            }
        }

        if (is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return true;
    }

    /** {@inheritdoc} */
    public function get(callable $callback = null): mixed
    {
        $result = $this->acquire();

        if ($result && is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return $result;
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     */
    abstract protected function isOwner(string $currentOwner): bool;

    /** {@inheritdoc} */
    public function getOwner(): string
    {
        return $this->owner;
    }

    protected function timestamp(): int|float
    {
        if (2 === sscanf(microtime(), '%f %f', $usec, $sec)) {
            return ($sec + $usec);
        }
        return microtime(true);
    }

}
