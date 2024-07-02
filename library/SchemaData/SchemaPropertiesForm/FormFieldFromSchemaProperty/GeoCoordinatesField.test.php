<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

use PHPUnit\Framework\TestCase;

class GeoCoordinatesFieldTest extends TestCase
{
    public function testFieldIsAGoogleMapsField()
    {
        $field   = new GeoCoordinatesField();
        $created = $field->create('schemaType', 'propertyName', ['GeoCoordinates']);
        $this->assertEquals('google_map', $created['type']);
    }
}
