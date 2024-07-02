<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

class StringField implements FormFieldFromSchemaProperty
{
    public function __construct(private FormFieldFromSchemaProperty $inner = new NullField())
    {
    }

    public function create(string $schemaType, string $propertyName, array $acceptedPropertyTypes): array
    {
        $field = $this->inner->create($schemaType, $propertyName, $acceptedPropertyTypes);

        if (!in_array('string', $acceptedPropertyTypes)) {
            return $field;
        }

        return array_merge($field, [
            'type'          => 'text',
            'default_value' => '',
            'required'      => 0,
        ]);
    }
}
