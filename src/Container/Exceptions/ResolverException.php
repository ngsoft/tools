<?php

declare(strict_types=1);

namespace NGSOFT\Container\Exceptions;

use NGSOFT\Container\ContainerResolver;

class ResolverException extends ContainerError
{

    public static function notTwice(object $resolver)
    {
        return new static(
                sprintf('Cannot add the same resolver [%s#%d] instance twice.', get_class($resolver), spl_object_id($resolver))
        );
    }

}