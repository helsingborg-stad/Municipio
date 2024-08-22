<?php

namespace Municipio\ExternalContent\Config;

interface ConfigFactoryInterface
{
    /**
     * Build the configuration.
     */
    public function create(): ISourceConfig;
}
