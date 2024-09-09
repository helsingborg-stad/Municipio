<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

interface SourceConfigInterface
{
    /**
     * Get the type of the source.
     *
     * @return string
     */
    public function getType(): string;
}
