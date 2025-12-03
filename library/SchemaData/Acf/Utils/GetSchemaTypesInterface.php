<?php

namespace Municipio\SchemaData\Acf\Utils;

interface GetSchemaTypesInterface
{
    /**
     * Get the schema types.
     *
     * @return array The all available schema @types.
     */
    public function getSchemaTypes(): array;
}
