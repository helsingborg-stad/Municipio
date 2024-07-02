<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

interface FormFieldFromSchemaProperty
{
    public function create(string $schemaType, string $propertyName, array $acceptedPropertyTypes): array;
}
