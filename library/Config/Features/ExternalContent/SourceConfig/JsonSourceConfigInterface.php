<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

interface JsonSourceConfigInterface extends SourceConfigInterface
{
    /**
     * Get the file path.
     * 
     * @return string
     */
    public function getFilePath(): string;
}