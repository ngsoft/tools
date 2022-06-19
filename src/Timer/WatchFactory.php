<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use NGSOFT\DataStructure\Map;

class WatchFactory
{

    protected const DEFAULT_WATCH = 'default';

    protected Map $map;

    public function __construct(protected bool $highResolution = false)
    {
        $this->map = new Map();
    }

    /**
     * Get a watch
     */
    public function getWatch(mixed $task = self::DEFAULT_WATCH): StopWatch
    {

        if ($this->map->has($task)) {
            $instance = new StopWatch($task, $this->highResolution);
            $this->map->set($task, $instance);
        }
        return $this->map->get($task);
    }

    /**
     * Starts the clock
     */
    public function start(mixed $task = self::DEFAULT_WATCH, int|float|null $startTime = null): bool
    {
        return $this->getWatch($task)->start($startTime);
    }

    /**
     * Resumes the clock (only if paused)
     *
     * @return bool
     */
    public function resume(mixed $task = self::DEFAULT_WATCH): bool
    {
        return $this->getWatch($task)->resume();
    }

    /**
     * Resets the clock
     */
    public function reset(mixed $task = self::DEFAULT_WATCH): void
    {
        $this->getWatch($task)->reset();
    }

    /**
     * Resets all the clocks
     */
    public function resetAll(): void
    {
        $this->map->clear();
    }

    /**
     * Pauses the clock
     */
    public function pause(mixed $task = self::DEFAULT_WATCH, bool &$success = null): StopWatchResult
    {
        return $this->getWatch($task)->pause($success);
    }

    /**
     * Stops the clock
     */
    public function stop(mixed $task = self::DEFAULT_WATCH, bool &$success = null): StopWatchResult
    {
        return $this->getWatch($task)->stop($success);
    }

    /**
     * @return \Generator|StopWatchResult[]
     */
    public function getLaps(): iterable
    {
        yield from $this->getWatch($task)->getLaps();
    }

    /**
     * Adds a lap time
     */
    public function lap(?string $label = null): bool
    {
        return $this->getWatch($task)->lap($label);
    }

}
