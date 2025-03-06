<?php

namespace Municipio\ExternalContent\Filter\SchemaObjectsFilter;

use Spatie\SchemaOrg\BaseType;

interface SchemaObjectsFilterInterface
{
    /**
     * Filter schema objects
     *
     * @param BaseType[] $schemaObjects
     * @return BaseType[]
     */
    public function filter(array $schemaObjects): array;
}
