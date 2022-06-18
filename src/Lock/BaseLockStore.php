<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Interfaces\LockStore;
use function random_string;

abstract class BaseLockStore implements LockStore
{

    protected const KEY_UNTIL = 0;
    protected const KEY_OWNER = 1;
    protected const FOREVER = 3600;

    protected int|float $until = 0;

    public function __construct(
            public readonly string $name,
            protected int|float $seconds = 0,
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
        if ( ! $this->isExpired($this->until)) {
            return true;
        }

        // called release before acquire
        if ($this->until === 0 && $data = $this->read()) {
            // possible with $owner override (shared lock)
            if ($this->getOwner() === $data[self::KEY_OWNER]) {
                $until = $data[self::KEY_UNTIL];
                if ( ! $this->isExpired($until)) {
                    $this->until = $until;
                    return true;
                }
            }
        }

        return false;
    }

    /** {@inheritdoc} */
    public function release(): bool
    {

        if ($this->isAcquired()) {
            $this->forceRelease();
            return true;
        }
        return false;
    }

    /**
     * Reads data from the driver
     */
    abstract protected function read(): array|false;

    /**
     * Write data from the driver
     * and updates $this->until if successful
     */
    abstract protected function write(int|float $until): bool;

    /** {@inheritdoc} */
    public function acquire(): bool
    {

        if ($this->isAcquired()) {
            return true;
        }

        $canAcquire = false;
        if ($lock = $this->read()) {
            if ($this->isExpired($lock[self::KEY_UNTIL])) {
                $canAcquire = true;
            } elseif ($this->getOwner() === $lock[self::KEY_OWNER]) {
                $this->until = $lock[self::KEY_UNTIL];
                return true;
            }
        } else { $canAcquire = true; }

        if ($canAcquire) {

            $retry = 0;

            $seconds = $this->seconds > 0 ? $this->seconds : self::FOREVER;

            while ($retry < 3) {
                $this->wait();
                if ($this->write($until = $this->timestamp() + $seconds)) {
                    $this->until = $until;
                    return true;
                }
                $retry ++;
            }
        }

        return false;
    }

    /** {@inheritdoc} */
    public function block(int|float $seconds, mixed $callback = null): mixed
    {

        $starting = $this->timestamp();
        while ( ! $this->acquire()) {
            $this->wait();
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

    protected function wait(int $ms = 0): void
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

    protected function getHashedName(): string
    {
        return hash('MD5', $this->name);
    }

}
