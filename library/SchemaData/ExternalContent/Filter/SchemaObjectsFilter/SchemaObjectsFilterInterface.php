<?php

namespace Municipio\SchemaData\ExternalContent\Filter\SchemaObjectsFilter;

use Municipio\Schema\BaseType;

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
