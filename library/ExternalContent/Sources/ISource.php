<?php

namespace Municipio\ExternalContent\Sources;

use Spatie\SchemaOrg\BaseType;
use WP_Query;

interface ISource
{
    public function getObject(string|int $id): null|BaseType;

    /**
     * @param SchemaSourceFilter|null $filter
     * @return (BaseType)[]
     */
    public function getObjects(?WP_Query $query = null): array;
    public function getPostType(): string;
    public function getId(): string;
};
