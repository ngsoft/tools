<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

class FileContents implements \IteratorAggregate, \ArrayAccess, \Countable, \Stringable, \JsonSerializable
{

    public function __construct(
            protected File $file,
            protected array $lines = []
    )
    {

    }

    public function reload()
    {
        $this->lines = $this->file->readAsArray();
    }

    protected function readLine(int $offset): string|null
    {
        $offset --;
        return $this->lines[$offset] ?? null;
    }

    protected function writeLine(int|null $offset, string $value): void
    {

        if ( ! is_int($offset)) {
            $this->lines[] = $value;
            return;
        }
        $offset --;
        $this->lines[$offset] = $value;
    }

    protected function removeLine(int $offset): void
    {
        $offset --;
        unset($this->lines[$offset]);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->readLine($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->writeLine($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->removeLine($offset);
    }

    public function count(): int
    {
        return count($this->lines);
    }

    public function getIterator(): \Traversable
    {

        foreach ($this->lines as $index => $line) {
            yield $index + 1 => $line;
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return implode("\n", $this->lines);
    }

}
