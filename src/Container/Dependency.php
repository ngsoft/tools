<?php

declare(strict_types=1);

namespace NGSOFT\Container;

class Dependency
{

    protected const REQUIRED = 0;
    protected const OPTIONAL = 1;

    protected static $instances = [[], []];

    /** @var static[] */
    protected array $dependencies = [];

    public function create(string $dependency, bool $optional): static
    {
        return self::$instances[(int) $optional][$dependency] ??= new static($dependency, $optional);
    }

    public static function getRequiredDependencies(): array
    {
        return self::$instances[self::REQUIRED];
    }

    private function __construct(
            public readonly string $id,
            public readonly bool $optional
    )
    {

    }

    public function addDependency(string $dependency, bool $optional): void
    {
        $this->dependencies[$dependency] = $this->create($dependency, $optional);
    }

    /**
     * @return static[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

}
