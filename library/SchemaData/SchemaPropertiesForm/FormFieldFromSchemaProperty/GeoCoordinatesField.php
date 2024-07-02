<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

class GeoCoordinatesField implements FormFieldFromSchemaProperty
{
    public function __construct(private FormFieldFromSchemaProperty $inner = new NullField())
    {
    }

    public function create(string $schemaType, string $propertyName, array $acceptedPropertyTypes): array
    {
        $field = $this->inner->create($schemaType, $propertyName, $acceptedPropertyTypes);

        if (!in_array('GeoCoordinates', $acceptedPropertyTypes)) {
            return $field;
        }

        return array_merge($field, [
            'type'       => 'google_map',
            'required'   => 0,
            'center_lat' => '',
            'center_lng' => '',
            'zoom'       => '',
            'height'     => '',
        ]);
    }
}
