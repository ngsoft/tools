<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use BadMethodCallException;

trait Unserializable
{

    /** {@inheritdoc} */
    final public function __sleep(): array
    {
        throw new BadMethodCallException('Cannot serialize ' . static::class);
    }

    /** {@inheritdoc} */
    final public function __wakeup(): void
    {
        throw new BadMethodCallException('Cannot unserialize ' . static::class);
    }

}
