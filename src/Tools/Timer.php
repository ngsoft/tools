<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use LogicException;
use NGSOFT\{
    DataStructure\Map, Traits\StringableObject
};
use Stringable;

final class Timer implements Stringable
{

    use StringableObject;

    public const FORMAT_PRECISE = 'p';
    public const FORMAT_MILLISECONDS = 'ms';
    public const FORMAT_SECONDS = 's';
    protected const DEFAULT_TASK = 'default';

    protected bool $started = false;
    protected float|int $timestamp;
    protected int|float $elapsed = 0;
    protected mixed $task;
    protected static ?Map $map;

    public function __construct(mixed $task)
    {
        $this->task = $task;
        $this->startTimer();
    }

    public function startTimer(): static
    {
        if (!$this->started) {
            $this->started = true;
            $this->timestamp = static::getTimestamp();
        }

        return $this;
    }

    public function stopTimer(): int|float
    {

        if ($this->started) {
            $this->elapsed += (static::getTimestamp() - $this->timestamp);
            $this->started = false;
        }
        return $this->elapsed;
    }

    public function readTimer(): int|float
    {

        if ($this->started) {
            return $this->elapsed + (static::getTimestamp() - $this->timestamp);
        }

        return $this->elapsed;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function __debugInfo(): array
    {
        return [];
    }

    protected static function getTimestamp(): int|float
    {
        static $hrtime;
        $hrtime = $hrtime ?? function_exists('hrtime');

        if ($hrtime) {
            return (hrtime(true) / 1e+9);
        }

        return microtime(true);
    }

    protected static function getTask(mixed $task): ?static
    {
        return self::getMap()->get($task);
    }

    protected static function getMap(): Map
    {
        self::$map = self::$map ?? new Map();
        return self::$map;
    }

    protected static function formatTime(int|float $time, string $format): int|float
    {


        switch ($format) {
            case self::FORMAT_MILLISECONDS:
                $result = round($time * 1000, 2);
                break;
            case self::FORMAT_SECONDS:
                $result = round($time, 3);
                break;
            case self::FORMAT_PRECISE:
            default:
                $result = $time * 1000;
                break;
        }
        return $result;
    }

    /**
     * Starts or resume the timer
     *
     * @param mixed $task
     * @return static
     */
    public static function start(mixed $task = self::DEFAULT_TASK): static
    {
        $map = self::getMap();

        if (!$map->has($task)) {
            $map->set($task, new static($task));
        }
        return self::getTask($task)->startTimer();
    }

    /**
     * Returns the time elapsed
     * @param mixed $task
     * @param string $format
     * @return int|float
     * @throws LogicException
     */
    public static function read(mixed $task = self::DEFAULT_TASK, string $format = self::FORMAT_MILLISECONDS): int|float
    {

        $result = self::getTask($task)?->readTimer();

        if ($result === null) {
            throw new LogicException('Reading timer when task has not been initiated.');
        }

        return self::formatTime($result, $format);
    }

    /**
     * Stops the timer
     *
     * @param mixed $task
     * @param string $format
     * @return int|float
     * @throws LogicException
     */
    public static function stop(mixed $task = self::DEFAULT_TASK, string $format = self::FORMAT_MILLISECONDS): int|float
    {

        if (!self::getMap()->has($task)) {
            throw new LogicException('Stopping timer when task has not been initiated.');
        }
        return self::formatTime(self::getTask($task)->stopTimer(), $format);
    }

    /**
     * Resets a specific timer
     * @param mixed $task
     * @return void
     */
    public static function reset(mixed $task = self::DEFAULT_TASK): void
    {
        self::getMap()->delete($task);
    }

    /**
     * Resets ALL timers.
     * @return void
     */
    public static function resetAll(): void
    {
        self::getMap()->clear();
    }

}
