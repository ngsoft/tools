<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

class FileContents implements \IteratorAggregate, \ArrayAccess, \Countable, \Stringable, \JsonSerializable
{

    protected bool $loaded = false;

    public function __construct(
            protected File $file,
            protected array $lines = []
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
     * Reloads file contents
     */
    public function reload(): void
    {
        $this->lines = $this->file->readAsArray();
        $this->loaded = true;
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
    protected function readLine(int $offset): string|null
    {
        $this->load();
        $offset --;
        return $this->lines[$offset] ?? null;
    }

    /**
     * replaces / adds a line
     */
    protected function writeLine(int|null $offset, string $value): void
    {
        $this->load();
        if ( ! is_int($offset)) {
            $this->lines[] = $value;
            return;
        }
        $offset --;
        $this->lines[$offset] = $value;
    }

    /**
     * Delete a line
     */
    protected function removeLine(int $offset): void
    {
        $this->load();
        $offset --;
        unset($this->lines[$offset]);
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
        $this->writeLine($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        $this->removeLine($offset);
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
        foreach ($this->lines as $index => $line) {
            yield $index + 1 => $line;
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

}
