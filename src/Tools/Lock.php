<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use NGSOFT\Interfaces\LockStore;

class Lock
{

    protected const EXTENSION = '.lock';

    protected string $name;
    protected int $pid;
    protected ?LockStore $store;

    public function __construct(
            string $name,
            ?LockStore $store = null
    )
    {
        $this->name = $name;
        $this->store = $store;
        $this->pid = getmypid();
    }

    public function acquire(): bool
    {

    }

    public function release(): bool
    {

    }

    public function forceRelease(): bool
    {

    }

}
