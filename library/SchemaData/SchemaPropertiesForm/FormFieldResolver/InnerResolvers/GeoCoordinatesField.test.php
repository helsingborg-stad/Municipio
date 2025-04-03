<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use PHPUnit\Framework\TestCase;

class GeoCoordinatesFieldTest extends TestCase
{
    public function testFieldIsAGoogleMapsField()
    {
        $field = new GeoCoordinatesField(['GeoCoordinates'], new EmptyField());
        $this->assertEquals('google_map', $field->resolve()['type']);
    }
}
