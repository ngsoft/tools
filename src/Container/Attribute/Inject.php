<?php

declare(strict_types=1);

namespace NGSOFT\Container\Attribute;

use Attribute,
    NGSOFT\Container\Exceptions\ContainerError;
use function get_debug_type;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER)]
final class Inject
{

    public string $name = '';
    public array $parameters = [];

    public function __construct(
            string|array $name = ''
    )
    {

        if ( ! is_string($name)) {
            foreach ($name as $index => $id) {

                if ( ! is_string($id)) {
                    throw new ContainerError(
                                    sprintf(
                                            "#[Inject([%s => 'id'])] expects 'id' to be a string, %s given.",
                                            var_export($index, true),
                                            get_debug_type($id)
                                    )
                    );
                }

                $this->parameters[$index] = $id;
            }
        } else { $this->name = $name; }
    }

}
