<?php

namespace Municipio\ExternalContent\Config\Providers;

use Municipio\ExternalContent\Config\ISourceConfig;

abstract class SourceConfig implements ISourceConfig
{
    public function __construct(protected $postType, protected $schemaObjectType)
    {
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

    public function getSchemaObjectType(): string
    {
        return $this->schemaObjectType;
    }
}
