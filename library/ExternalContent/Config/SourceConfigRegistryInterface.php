<?php

namespace Municipio\ExternalContent\Config;

interface SourceConfigRegistryInterface
{
    /**
     * Get all source configurations.
     *
     * @return SourceConfigInterface[] The source configurations.
     */
    public function getSourceConfigurations(): array;
}
