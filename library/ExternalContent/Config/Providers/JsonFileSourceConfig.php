<?php

namespace Municipio\ExternalContent\Config\Providers;

use Municipio\ExternalContent\Config\IJsonFileSourceConfig;

class JsonFileSourceConfig extends SourceConfig implements IJsonFileSourceConfig
{
    public function __construct(
        protected string $postType,
        protected string $schemaObjectType,
        protected string $file
        )
    {
        parent::__construct($postType, $schemaObjectType);
    }

    public function getFile(): string
    {
        return $this->file;
    }
}