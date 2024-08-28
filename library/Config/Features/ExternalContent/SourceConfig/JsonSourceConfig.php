<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

class JsonSourceConfig implements JsonSourceConfigInterface
{
    public function __construct(
        private string $filePath,
    ) {
    }

    public function getType(): string
    {
        return 'json';
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
