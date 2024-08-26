<?php

namespace Municipio\ExternalContent\Sources;

use Spatie\SchemaOrg\BaseType;
use WP_Query;

interface SourceInterface
{
    public function getObject(string|int $id): ?BaseType;

    /**
     * @param WP_Query|null $query
     * @return (BaseType)[]
     */
    public function getObjects(?WP_Query $query = null): array;
    public function getPostType(): string;
    public function getId(): string;
    public function getSchemaObjectType(): string;
};
