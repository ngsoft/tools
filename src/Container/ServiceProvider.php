<?php

declare(strict_types=1);

namespace NGSOFT\Container;

interface ServiceProvider
{

    public function provide(Container $container): void;
}
