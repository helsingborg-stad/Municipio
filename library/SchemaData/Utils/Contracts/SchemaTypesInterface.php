<?php

namespace Municipio\SchemaData\Utils\Contracts;

interface SchemaTypesInterface
{
    /**
     * Get all available schema types
     *
     * @return array
     */
    public function getSchemaTypes(): array;
}
