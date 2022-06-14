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

    public static function startTaskWithStartTime(mixed $task, int|float $startTime, bool $highResolution = false): static
    {
        $watch = new static($task, $highResolution);
        $watch->start($startTime);
        return $watch;
    }

    /**
     * @param mixed|callable $task can be anything
     * @param bool $highResolution if True use hrtime() if available, else use microtime()
     */
    public function __construct(
            protected mixed $task = self::DEFAULT_TASK,
            protected bool $highResolution = true
    )
    {
        $this->state = State::from(State::IDLE);
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

        $callable = $this->task;
        $this->reset();
        $this->start();
        $callable(...$arguments);
        return $this->stop();
    }

    /**
     * Starts the clock, also reset the laps
     *
     * @param int|float|null $startTime Set the start time
     * @return bool
     */
    public function start(int|float|null $startTime = null): bool
    {
        if ($this->state->is(State::IDLE, State::PAUSED)) {
            $this->startTime = ($startTime ?? $this->timestamp());
            $this->setState(State::STARTED);
            return true;
        }
        return false;
    }

    /**
     * Resumes the clock (only if paused)
     *
     * @return bool
     */
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
     * @return void
     */
    public function reset(): void
    {
        $this->startTime = $this->runTime = 0;
        $this->laps = [];
        $this->setState(State::IDLE);
    }

    /**
     * Pauses the clock
     *
     * @param bool $success True if operation succeeded
     * @return StopWatchResult Current time
     */
    public function pause(bool &$success = null): StopWatchResult
    {
        $success = false;
        if ($this->isStarted()) {
            $this->runTime += ($this->timestamp() - $this->startTime);
            $this->laps[] = $this->runTime;
            $this->setState(State::PAUSED);
        }
        return $this->read();
    }

    /**
     * Stops the clock
     *
     * @param bool $success True if operation succeeded
     * @return StopWatchResult Current time
     */
    public function stop(bool &$success = null): StopWatchResult
    {
        $success = false;
        if ($this->isStarted()) {
            $this->pause();
        }

        if ($this->isPaused()) {
            $this->setState(State::STOPPED);
            $success = true;
        }

        return $this->read();
    }

    protected function readRaw(): int|float
    {
        if ($this->isStarted()) {
            return $this->runTime + ($this->timestamp() - $this->startTime);
        }
        return $this->runTime;
    }

    /**
     * Reads the clock
     *
     * @return StopWatchResult
     */
    public function read(): StopWatchResult
    {
        return StopWatchResult::create($this->readRaw());
    }

    /**
     * @return StopWatchResult[]
     */
    public function getLaps(): iterable
    {
        $prev = 0;
        foreach ($this->laps as $index => $time) {

            $ctime = $time - $prev;
            $prev = $time;

            yield $index + 1 => StopWatchResult::create($ctime);
        }
    }

    /**
     * Adds a lap time
     *
     * @return bool
     */
    public function lap(): bool
    {
        if (!$this->isStarted()) {
            return false;
        }
        $this->laps[] = $this->readRaw();
        return true;
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
        if ($state instanceof State) {
            $this->state = $state;
        } else { $this->state = State::from($state); }
    }

    protected function timestamp(): int|float
    {
        static $hrtime;
        $hrtime = $hrtime ?? function_exists('hrtime');

        if ($this->highResolution && $hrtime) {
            return (hrtime(true) / 1e+9);
        }

        return $this->safeMicrotime();
    }

    protected function safeMicrotime(): int|float
    {
        if (2 === sscanf(microtime(), '%f %f', $usec, $sec)) {
            return ($sec + $usec);
        }
        return microtime(true);
    }

}
