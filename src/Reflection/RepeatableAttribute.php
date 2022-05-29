<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use ArrayAccess,
    Countable,
    IteratorAggregate,
    NGSOFT\Traits\ArrayAccessCountable;

class RepeatableAttribute implements Countable, IteratorAggregate, ArrayAccess {

    use ArrayAccessCountable;

    /** {@inheritdoc} */
    public function __unserialize(array $data): void {
        $this->storage = $data;
    }

    /** {@inheritdoc} */
    public function __serialize(): array {
        return $this->storage;
    }

}