<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class Source implements SourceInterface
{
    public function __construct(
        private string $postType,
        private string $schemaObjectType
    ) {
    }

    public function getObject(string|int $id): null|BaseType
    {
        return null;
    }

    public function getObjects(?WP_Query $query = null): array
    {
        return [];
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

    public function getId(): string
    {
        return '';
    }

    public function getSchemaObjectType(): string
    {
        return $this->schemaObjectType;
    }
}
