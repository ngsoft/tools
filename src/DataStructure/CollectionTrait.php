<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\Tools;

/**
 * @phan-file-suppress PhanTypeMismatchReturn
 */
trait CollectionTrait
{

    /**
     * Returns a new iterable indexed by id
     */
    abstract public function entries(Sort $sort = Sort::ASC): iterable;
}
