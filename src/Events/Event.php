<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use NGSOFT\Traits\StoppableEventTrait,
    Psr\EventDispatcher\StoppableEventInterface;

/**
 * A base Stoppable Event
 */
abstract class Event implements StoppableEventInterface
{

    use StoppableEventTrait;
}
