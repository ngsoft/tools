<?php

declare(strict_types=1);

namespace NGSOFT\Tools\TypeCheck;

class Intersection implements \Stringable
{

    protected array $types = [];

    public function __construct(
            string|iterable $types
    )
    {
        if (is_string($types)) {
            $types = explode('&', $types);
        }

        $this->types = array_values(array_unique(iterable_to_array($types)));
    }

    public function check(mixed $value): bool
    {

        foreach ($types as $type) {

        }
    }

    public function __toString(): string
    {
        return implode('&', $this->types);
    }

}
