<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    OutOfBoundsException,
    Stringable,
    Throwable;
use function get_debug_type,
             NGSOFT\Tools\pause;

class SimpleObject implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Stringable
{

    use ArrayAccessCommon;

    protected string $filename = '';
    protected ?self $parent = null;
    protected ?string $hash = null;
    protected ?string $lock = null;

    /** @var static[] */
    protected array $children = [];

    public function __destruct()
    {
        if ($this->lock) {
            unlink($this->lock);
        }
    }

    /**
     * Instanciates a new instance using the given json file and syncs it (writes to it when keys are added/removed)
     *
     * @param string $filename
     * @param bool $recursive
     * @return static
     */
    public static function syncJsonFile(string $filename, bool $recursive = true): static
    {
        try {
            $instance = static::fromJsonFile($filename, $recursive);
            $instance->filename = $filename;
            $instance->hash = $instance->getHash();
        } catch (Throwable) {
            $instance = static::create([], $recursive);
            $instance->filename = $filename;
        }


        return $instance;
    }

    protected function getLock(int|float $timeout = 10): bool
    {
        $lock = "{$this->filename}.lock";

        $start = microtime(true);

        while (file_exists($lock)) {
            if (microtime(true) > ($timeout + $start)) {
                var_dump('timeout');
                return false;
            }
            pause(.01);
        }

        return true;
    }

    protected function lock(callable $process): bool
    {
        $lock = "{$this->filename}.lock";

        if ($this->getLock() && touch($lock)) {
            $this->lock = $lock;
            $process();
            if (unlink($lock)) {
                $this->lock = null;
                return true;
            }
        }

        return false;
    }

    /**
     * Write to json file when modified
     * @internal
     * @return void
     */
    protected function update(): void
    {
        if ( ! empty($this->filename)) {

            $this->lock(function () {
                if ($this->saveToJson($this->filename)) {
                    $this->hash = $this->getHash();
                }
            });
        } else { $this->parent?->update(); }
    }

    /**
     * Reloads json file if modified by another process/instance
     *
     * @return void
     */
    protected function load(): void
    {
        if ( ! empty($this->filename)) {
            // can check concurrent modifications
            $hash = $this->getHash();

            //file does not exists: null === null, no need to load
            if ($hash !== $this->hash) {
                $this->lock(function () use ($hash) {
                    if ($string = file_get_contents($this->filename)) {
                        $array = json_decode($string, true);
                        if (is_array($array)) {
                            $this->storage = $array;
                            $this->hash = $this->getHash();
                        }
                    }
                });
            }
        } else { $this->parent?->load(); }
    }

    protected function getHash(): ?string
    {
        if ( ! is_file($this->filename)) {
            return null;
        }

        while (($hash = hash_file('crc32', $this->filename) ) === false) {

        }
        return $hash;

        return hash_file('crc32', $this->filename) ?: null;
    }

    protected function assertValidImport(array $import): void
    {

        foreach (array_keys($import) as $offset) {
            if ( ! is_int($offset) && ! is_string($offset)) {
                throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
            }
        }
    }

    protected function append(mixed $offset, mixed $value): void
    {

        if (null === $offset) {
            $this->storage[] = $value;
            return;
        }

        if ( ! is_int($offset) && ! is_string($offset)) {
            throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int|null, %s given.', static::class, get_debug_type($offset)));
        }
        unset($this->storage[$offset], $this->children[$offset]);
        if ($value instanceof self) $value = $value->storage;
        $this->storage[$offset] = $value;
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful
     *
     * @param mixed $value
     * @return int|string|null
     */
    public function search(mixed $value): int|string|null
    {
        if ($value instanceof self) $value = $value->storage;
        $offset = array_search($value, $this->storage, true);
        return $offset === false ? null : $offset;
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    /** {@inheritdoc} */
    public function &offsetGet(mixed $offset): mixed
    {
        $this->load();
        $value = null;
        if (null === $offset) {
            $this->storage[] = [];
            $offset = array_key_last($this->storage);
        }
        if (array_key_exists($offset, $this->storage)) {
            $value = &$this->storage[$offset];
            if ($this->recursive && is_array($value)) {

                if (isset($this->children[$offset])) {
                    return $this->children[$offset];
                }

                $instance = $this->getNewInstance();
                $instance->parent = $this;
                $instance->storage = &$value;
                $this->children[$offset] = $instance;
                return $instance;
            }
        }
        return $value;
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->append($offset, $value);
        $this->update();
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$offset], $this->children[$offset]);
        $this->update();
    }

    /** {@inheritdoc} */
    public function &__get(string $name): mixed
    {
        $value = $this->offsetGet($name);
        return $value;
    }

    /** {@inheritdoc} */
    public function __set(string $name, mixed $value): void
    {
        $this->offsetSet($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset(string $name): void
    {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->storage, $this->recursive, $this->filename];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {
        list($this->storage, $this->recursive, $this->filename) = $data;
    }

}
