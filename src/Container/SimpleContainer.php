<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Container\Resolvers\ClosureResolver;

/**
 * Container with only basic functionality
 */
final class SimpleContainer extends ContainerAbstract
{

    public function __construct(array $definitions = [])
    {
        parent::__construct($definitions);

        $this->addResolutionHandler(new ClosureResolver());
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers);
    }

    protected function isResolved(string $id): bool
    {
        $this->handleServiceProvidersResolution($id);

        if (array_key_exists($id, $this->definitions)) {
            return $this->definitions[$id] instanceof Closure === false;
        }
        throw new NotFoundException($this, $id);
    }

}
