<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

interface GetFormFieldsBySchemaPropertiesInterface
{
    /**
     * Get the form fields by schema properties.
     *
     * @param string $schemaType The schema type.
     * @param array $schemaProperties The schema properties.
     * @return array The form fields.
     */
    public function getFormFieldsBySchemaProperties(string $schemaType, array $schemaProperties): array;
}
