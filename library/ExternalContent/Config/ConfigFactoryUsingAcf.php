<?php

namespace Municipio\ExternalContent\Config;

use AcfService\Contracts\GetFields;

class ConfigFactoryUsingAcf implements ConfigFactoryInterface
{
    public function __construct(private GetFields $acfService)
    {
    }

    public function build(): array
    {
        return [];
    }
}
