<?php

declare(strict_types=1);

namespace NGSOFT\Container\Exceptions;

class NotFound extends ContainerError implements \Psr\Container\NotFoundExceptionInterface
{

    static function for(string $id, \Throwable $previous = null): static
    {
        return new static(
                sprintf(
                        'No entry or class found for [%s]',
                        $id
                ),
                previous: $previous
        );
    }

}
