<?php

namespace Municipio\ExternalContent\SchemaObjectsFilter;

use Spatie\SchemaOrg\BaseType;

interface SchemaObjectsFilterInterface
{
    /**
     * Apply filter to schema objects.
     *
     * @param BaseType[] $schemaObjects Schema objects to filter.
     * @return BaseType[] Filtered schema objects.
     */
    public function applyFilter(array $schemaObjects): array;
}
