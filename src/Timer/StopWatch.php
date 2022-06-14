<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

use RuntimeException;
use function get_debug_type;

class StopWatch
{

    protected const DEFAULT_TASK = 'default';

    protected State $state;
    protected int|float $startTime = 0;
    protected int|float $runTime = 0;
    protected array $laps = [];

    /**
     * Starts a callable and returns result time
     *
     * @param callable $task
     * @param mixed $arguments
     * @return StopWatchResult
     */
    public static function startTask(callable $task, mixed ...$arguments): StopWatchResult
    {
        $watch = new static($task);
        return $watch->executeTask($arguments);
    }

    public static function startTaskWithStartTime(mixed $task, int|float $startTime, bool $highResolution = false)
    {

        $watch = new static($task, $highResolution);

        $watch->start($startTime);

        return $watch;
    }

    public function __construct(
            protected mixed $task = self::DEFAULT_TASK,
            protected bool $highResolution = true
    )
    {
        $this->state = State::IDLE();
    }

    public function getTask(): mixed
    {
        return $this->task;
    }

    public function executeTask(array $arguments = []): StopWatchResult
    {
        if (!is_callable($this->task)) {
            throw new RuntimeException(sprintf('Task of type %s is not callable.', get_debug_type($this->task)));
        }
    }

    /**
     * Starts the clock
     *
     * @param int|float|null $startTime Set the start time
     * @return bool
     */
    public function start(int|float|null $startTime = null): bool
    {
        if ($this->state->is(State::IDLE, State::PAUSED)) {
            $this->startTime = $startTime ?? $this->timestamp();

            $this->setState(State::STARTED());

            return true;
        }
        return false;
    }

    public function resume(): bool
    {
        if (!$this->state->is(State::PAUSED)) {
            return false;
        }
        return $this->start();
    }

    /**
     * Resets the clock
     *
     * @return bool
     */
    public function reset(): bool
    {
        $this->startTime = $this->runTime = 0;
        $this->laps = [];
        $this->state = State::IDLE();

        return true;
    }

    /**
     * Pauses the clock
     *
     * @return StopWatchResult Current time
     */
    public function pause(): StopWatchResult
    {
        if ($this->state !== State::STARTED()) {
            throw new RuntimeException('Cannot pause the clock as it have not yet been started');
        }

        $current = $this->timestamp();
        $this->runTime += ($current - $this->startTime);
        $this->state = State::PAUSED();
    }

    /**
     * Stops the clock
     *
     * @return StopWatchResult Current time
     */
    public function stop(): StopWatchResult
    {

    }

    /**
     * Reads the clock
     *
     * @return StopWatchResult
     */
    public function read(): StopWatchResult
    {

    }

    /**
     * Adds a lap time
     *
     * @return bool
     */
    public function lap(): bool
    {

    }

    public function isStarted(): bool
    {
        return $this->state->is(State::STARTED);
    }

    public function isPaused(): bool
    {
        return $this->state->is(State::PAUSED);
    }

    public function isStopped(): bool
    {
        return $this->state->is(State::STOPPED, State::IDLE);
    }

    protected function setState(State|int $state): void
    {
        $this->state = State::from($state);
    }

    protected function timestamp(): int|float
    {
        static $hrtime;
        $hrtime = $hrtime ?? function_exists('hrtime');

        if ($this->highResolution && $hrtime) {
            return (hrtime(true) / 1e+9);
        }

        return microtime(true);
    }

}
