<?php

namespace Municipio\ExternalContent\Config;

interface ConfigFactoryInterface
{
    /**
     * Build the configuration.
     *
     * @return \Municipio\ExternalContent\Config\ISourceConfig[]
     */
    public function build(): array;
}
