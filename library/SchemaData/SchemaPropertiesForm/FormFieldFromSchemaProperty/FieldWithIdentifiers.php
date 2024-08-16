<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

class FieldWithIdentifiers implements FormFieldFromSchemaProperty
{
    public function __construct(private FormFieldFromSchemaProperty $inner = new NullField())
    {
    }

    public function create(string $schemaType, string $propertyName, array $acceptedPropertyTypes): array
    {
        $field      = $this->inner->create($schemaType, $propertyName, $acceptedPropertyTypes);
        $identifier = 'schema_' . $propertyName;

        return array_merge($field, [
            'key'   => $identifier,
            'label' => $propertyName,
            'name'  => $identifier,
        ]);
    }
}
