<?php

namespace Municipio\SchemaData\Utils\Contracts;

interface SchemaTypesInUseInterface
{
    /**
     * Get all Schema Types in use.
     * A schema type in use is a schema type that is connected to an existing post type.
     *
     * @return string[]
     */
    public function getSchemaTypesInUse(): array;
}
