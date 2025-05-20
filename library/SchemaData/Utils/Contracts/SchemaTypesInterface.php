<?php

namespace Municipio\SchemaData\Utils\Contracts;

interface SchemaTypesInterface
{
    /**
     * Get all available schema types
     *
     * @return string[]
     */
    public function getSchemaTypes(): array;
}
