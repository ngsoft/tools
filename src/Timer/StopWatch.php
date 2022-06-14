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
    protected int|float $runtime = 0;
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
     * @return bool
     */
    public function start(): bool
    {

        switch ($this->state) {
            case State::IDLE():
            case State::PAUSED():
                $this->startTime = $this->timestamp();
                $this->state = State::STARTED();
                return true;
            default :
                return false;
        }
    }

    public function resume(): bool
    {
        if ($this->state !== State::PAUSED()) {
            return false;
        }
        return $this->start();
    }

    public function reset(): bool
    {

        $this->startTime = $this->runtime = 0;
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
        $this->runtime += ($current - $this->startTime);
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
