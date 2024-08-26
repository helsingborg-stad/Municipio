<?php

namespace Municipio\ExternalContent\Config\Providers;

use Municipio\ExternalContent\Config\SourceConfigInterface;

abstract class SourceConfig implements SourceConfigInterface
{
    public function __construct(protected string $postType, protected string $schemaObjectType)
    {
    }

    public function getSchemaObjectType(): string
    {
        return $this->schemaObjectType;
    }

    public function getPostType(): string
    {
        return $this->postType;
    }
}
