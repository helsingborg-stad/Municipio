<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GeoCoordinatesFieldTest extends TestCase
{
    #[TestDox('field is a google map field')]
    public function testFieldIsAGoogleMapsField()
    {
        $field = new GeoCoordinatesField(['GeoCoordinates'], $this->getInnerResolver());
        $this->assertEquals('google_map', $field->resolve()['type']);
    }

    #[TestDox('field value gets sanitized from schema format to google maps format')]
    public function testFieldValueGetsSanitized()
    {
        $inner = $this->getInnerResolver();
        $inner->method('resolve')->willReturn([ 'value' => [ 'latitude' => 0, 'longitude' => 0 ] ]);

        $field = new GeoCoordinatesField(['GeoCoordinates'], $inner);

        $this->assertEquals([
            'lat' => 0,
            'lng' => 0
        ], $field->resolve()['value']);
    }

    #[TestDox('converts field value from json to array if needed')]
    public function testConvertsFieldValueFromJsonToArrayIfNeeded()
    {
        $inner = $this->getInnerResolver();
        $inner->method('resolve')->willReturn([ 'value' => json_encode([ 'latitude' => 0, 'longitude' => 0 ]) ]);

        $field = new GeoCoordinatesField(['GeoCoordinates'], $inner);

        $this->assertEquals([
            'lat' => 0,
            'lng' => 0
        ], $field->resolve()['value']);
    }

    private function getInnerResolver(): FormFieldResolverInterface|MockObject
    {
        return $this->createMock(FormFieldResolverInterface::class);
    }
}
