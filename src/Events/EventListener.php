<?php

declare(strict_types=1);

namespace NGSOFT\Events;

use Psr\EventDispatcher\ListenerProviderInterface;

class EventListener implements ListenerProviderInterface {

    /** @var ?ContainerInterface */
    private $container;

    /** @var object[] */
    private $events = [];

    public function getListenersForEvent(object $event): iterable {

    }

    /**
     * Set a Container to add Services
     *
     * @param ContainerInterface|null $container
     * @return void
     */
    public function setContainer(?ContainerInterface $container): void {
        $this->container = $container;
    }

}
