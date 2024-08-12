<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

class NullField implements FormFieldFromSchemaProperty
{
    public function create(string $schemaType, string $propertyName, array $acceptedPropertyTypes): array
    {
        return [
            'type' => null
        ];
    }
}
