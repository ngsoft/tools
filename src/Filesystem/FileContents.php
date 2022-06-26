<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

class FileContents implements \IteratorAggregate, \ArrayAccess, \Countable, \Stringable, \JsonSerializable
{

    public function __construct(
            protected File $file,
            protected array $lines = [],
            protected bool $loaded = false
    )
    {
        if ( ! empty($lines)) {
            $this->loaded = true;
        }
    }

    /**
     * Lazy-load contents
     */
    protected function load(): void
    {
        $this->loaded || $this->reload();
    }

    /**
     * Reorganize lines
     */
    public function refresh(): void
    {
        $this->load();
        $this->lines = array_values($this->lines);
    }

    /**
     * Reloads file contents
     */
    public function reload(): void
    {
        $this->lines = $this->file->readAsArray();
        $this->loaded = true;
    }

    /**
     * Clears the contents
     */
    public function clear(): void
    {
        $this->loaded = true;
        $this->lines = [];
    }

    /**
     * Run the callable with all the lines and replaces the contents with the return value
     */
    public function map(callable $callable): static
    {

        foreach ($this as $offset => $line) {
            $number = $offset;
            $result = $callable($line, $offset, $this);

            if (is_string($result) && $result !== $line) {
                $this->writeLine($result, $number);
            }
        }

        return $this;
    }

    /**
     * Save file contents
     */
    public function save(): bool
    {
        $this->load();
        return $this->file->write($this);
    }

    /**
     * Reads a line
     */
    public function readLine(int $offset): string|null
    {
        $this->load();
        $offset --;
        return $this->lines[$offset] ?? null;
    }

    /**
     * Replaces the entire contents
     */
    public function write(string|iterable $lines): static
    {
        $this->clear();

        if ( ! is_iterable($lines)) {
            $lines = [$lines];
        }

        foreach ($lines as $line) {
            $this->writeLine($line);
        }

        return $this;
    }

    /**
     * replaces / adds a line
     */
    public function writeLine(string $value, int|null $offset = null): static
    {
        $this->load();
        if ( ! is_int($offset)) {
            $this->lines[] = $value;
            return $this;
        }
        $offset = max(1, $offset);
        $offset --;
        $this->lines[$offset] = $value;

        return $this;
    }

    /**
     * Insert a line
     * if no offset defined will add to the begining of the file, if out of range will be added to the end of the file
     */
    public function insertLine(string $value, int|null $offset = null): static
    {
        $this->load();

        $offset = max(1, is_int($offset) ? $offset : 1);
        $offset --;
        if (array_key_exists($offset, $this->lines)) {
            array_splice($this->lines, $offset, 0, $value);
        } else { $this->lines[] = $value; }

        return $this;
    }

    /**
     * Delete a line, also reorganize lines
     */
    public function removeLine(int $offset): static
    {
        $this->load();
        $offset = max(1, $offset);
        $offset --;
        if (array_key_exists($offset, $this->lines)) {
            array_splice($this->lines, $offset, 1);
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->readLine($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->writeLine($value, $offset);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        $this->removeLine($offset);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        $this->load();
        return count($this->lines);
    }

    /**
     * @return \Traversable<int, string>
     */
    public function getIterator(): \Traversable
    {
        $this->load();
        $copy = $this->lines;
        for ($i = 0; $i < count($copy); $i ++ ) {
            $index = $i + 1;
            $line = $copy[$i];
            yield $index => $line;
        }
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return $this->__toString();
    }

    /** {@inheritdoc} */
    public function __toString(): string
    {
        $this->load();

        return implode("\n", $this->lines);
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->file];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {
        list($this->file) = $data;
    }

    public function __debugInfo(): array
    {
        $this->load();
        return $this->lines;
    }

}
