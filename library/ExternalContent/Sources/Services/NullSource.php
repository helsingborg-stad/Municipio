<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class NullSource implements SourceInterface
{
    public function getId(): string
    {
        return '';
    }
    public function getObject(string|int $id): ?BaseType
    {
        return null;
    }

    public function getObjects(?WP_Query $query = null): array
    {
        return [];
    }
    public function getPostType(): string
    {
        return '';
    }
    public function getSchemaObjectType(): string
    {
        return '';
    }
}
