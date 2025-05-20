<?php

namespace Municipio\SchemaData\Utils;

interface GetSchemaPropertiesWithParamTypesInterface
{
    /**
     * Get the schema properties with param types.
     *
     * @param string $schemaType The schema type.
     *
     * @return array<string, array<string>> The schema properties with param types. Example ['propertyName' => ['PostalAddress', 'PostalAddress[]']]
     */
    public function getSchemaPropertiesWithParamTypes(string $schemaType): array;
}
