<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @phan-file-suppress PhanTraitParentReference, PhanUndeclaredMethod
 */
trait HasName
{

    public readonly string $name;

    public function __construct(
            object|string $reflector
    )
    {
        parent::__construct($reflector);
        $this->name = $this->getName();
    }

}
