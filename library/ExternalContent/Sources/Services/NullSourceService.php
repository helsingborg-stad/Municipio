<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class NullSourceService implements ISource
{
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
        return '';
    }
    public function getId(): string
    {
        return '';
    }
}
