<?php

namespace Municipio\SchemaData\SchemaObjectFromPost\Utils;

interface IGetSchemaPropertiesWithParamTypes
{
    /**
     * Get the schema properties with param types.
     *
     * @param string $schemaType The schema type.
     *
     * @return array The schema properties with param types. Example ['propertyName' => ['PostalAddress', 'PostalAddress[]']]
     */
    public function getSchemaPropertiesWithParamTypes(string $schemaType): array;
}
