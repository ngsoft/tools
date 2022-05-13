<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use BadMethodCallException;

trait Unserializable {

    /** {@inheritdoc} */
    final public function __sleep() {
        throw new BadMethodCallException('Cannot serialize ' . static::class);
    }

    /** {@inheritdoc} */
    final public function __wakeup() {
        throw new BadMethodCallException('Cannot unserialize ' . static::class);
    }

    /** {@inheritdoc} */
    final public function __serialize() {
        throw new BadMethodCallException('Cannot serialize ' . static::class);
    }

    /** {@inheritdoc} */
    final public function __unserialize(array $data) {
        throw new BadMethodCallException('Cannot unserialize ' . static::class);
    }

}
