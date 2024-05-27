<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class SourceServiceWithPostType implements ISource
{
    public function __construct(private string $postType, private ISource $inner = new NullSourceService())
    {
    }

    public function getObject(string|int $id): null|BaseType
    {
        return $this->inner->getObject($id);
    }

    public function getObjects(?WP_Query $query = null): array
    {
        return $this->inner->getObjects($query);
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }
}
