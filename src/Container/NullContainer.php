<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use NGSOFT\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface;

class NullContainer implements ContainerInterface
{

    public function get(string $id): mixed
    {
        throw new NotFoundException($this, $id);
    }

    public function has(string $id): bool
    {
        return false;
    }

}
