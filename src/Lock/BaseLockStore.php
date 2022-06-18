<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Interfaces\LockStore;
use function random_string;

abstract class BaseLockStore implements LockStore
{

    protected const KEY_UNTIL = 0;
    protected const KEY_OWNER = 1;
    protected const KEY_PID = 2;

    protected int|float $until = 0;

    public function __construct(
            public readonly string $name,
            protected int|float $seconds,
            protected string $owner = '',
            protected bool $autoRelease = true
    )
    {

        $this->owner = empty($owner) ?
                random_string() . getmypid() :
                $owner;
    }

    public function __destruct()
    {
        if ($this->autoRelease && $this->isAcquired()) {
            $this->release();
        }
    }

    /** {@inheritdoc} */
    public function getRemainingLifetime(): float|int
    {
        return max($this->until - $this->timestamp(), 0);
    }

    /** {@inheritdoc} */
    public function isAcquired(): bool
    {
        return ! $this->isExpired($this->until);
    }

    /** {@inheritdoc} */
    public function block(int|float $seconds, mixed $callback = null): mixed
    {

        $starting = $this->timestamp();
        while ( ! $this->acquire()) {

            $this->waitFor();

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

    protected function waitFor(int $ms = 0): void
    {
        if ($ms === 0) {
            $ms = 100 + random_int(-10, 10);
        }

        usleep($ms * 1000);
    }

    protected function isExpired(int|float $until): bool
    {
        return $until < $this->timestamp();
    }

}
