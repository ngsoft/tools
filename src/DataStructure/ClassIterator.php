<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use InvalidArgumentException;

class ClassIterator implements \IteratorAggregate, \Countable, \Stringable
{

    use \NGSOFT\Traits\StringableObject;

    protected array $instances = [];

    public function __construct(
            protected string $className,
            iterable $instances = []
    )
    {
        if ( ! class_exists($className) && ! interface_exists($className)) {
            throw new InvalidArgumentException(sprintf('Class %s does not exists.', $className));
        }


        foreach ($instances as $instance) {
            $this->push($instance);
        }
    }

    public function count(): int
    {
        return count($this->instances);
    }

    public function getIterator(): \Traversable
    {
        $instances = $this->instances;
        yield from $instances;
    }

    protected function assertValidClass(object $instance): void
    {

        if ( ! is_subclass_of($instance, $this->className)) {
            throw new InvalidArgumentException(sprintf('Instance of %s does not implements %s', get_class($instance), $this->className));
        }
    }

    public function push(object $instance): void
    {
        $this->assertValidClass($instance);

        $this->pop($instance);
        $this->instances[] = $instance;
    }

    public function pop(?object $instance = null): ?object
    {


        if (is_null($instance)) {
            return array_pop($this->instances);
        }


        foreach (array_reverse(array_keys($this->instances)) as $index) {
            $current = $this->instances[$index];

            if ($current === $instance) {
                array_splice($this->instances, $index, 1);
                break;
            }
        }


        return $instance;
    }

    /**
     * Call Method in all the instances
     */
    public function call(string $method, mixed ...$arguments): \Traversable
    {

        foreach ($this as $instance) {

            yield $instance->{$method}(...$arguments);
        }
    }

    /**
     * Call Method in all the instances
     */
    public function apply(string $method, array $arguments = []): \Traversable
    {

        foreach ($this as $instance) {
            yield call_user_func_array([$instance, $method], $arguments);
        }
    }

    ////////////////////////////   All the magic is here   ////////////////////////////


    public function __clone(): void
    {

        foreach (array_keys($this->instances) as $index) {
            $this->instances[$index] = clone $this->instances[$index];
        }
    }

    public function __call(string $name, array $arguments): mixed
    {
        yield from $this->apply($name, $arguments);
    }

    public function __invoke(mixed ...$arguments): mixed
    {
        yield from $this->apply(__FUNCTION__, $arguments);
    }

    public function __serialize(): array
    {
        return [$this->className, $this->instances];
    }

    public function __unserialize(array $data): void
    {
        @list($this->className, $this->instances) = $data;
    }

}
