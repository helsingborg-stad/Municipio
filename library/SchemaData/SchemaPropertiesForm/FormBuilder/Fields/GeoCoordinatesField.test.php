<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use PHPUnit\Framework\TestCase;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GeoCoordinatesField;
use Municipio\Schema\Schema;

class GeoCoordinatesFieldTest extends TestCase
{
    public function testToArrayWithValidGeoCoordinates()
    {
        $geoCoordinates = Schema::geoCoordinates()
            ->latitude(59.3293)
            ->longitude(18.0686)
            ->address('Stockholm, Sweden');

        $field  = new GeoCoordinatesField('location', 'Location', $geoCoordinates);
        $result = $field->toArray();

        $this->assertEquals('google_map', $result['type']);
        $this->assertEquals('location', $result['key']);
        $this->assertEquals('location', $result['name']);
        $this->assertEquals('Location', $result['label']);
        $this->assertEquals([
            'lat'     => 59.3293,
            'lng'     => 18.0686,
            'address' => 'Stockholm, Sweden'
        ], $result['value']);
    }

    public function testToArrayWithNullGeoCoordinates()
    {
        $field  = new GeoCoordinatesField('location', 'Location', null);
        $result = $field->toArray();

        $this->assertEquals('google_map', $result['type']);
        $this->assertEquals('location', $result['key']);
        $this->assertEquals('location', $result['name']);
        $this->assertEquals('Location', $result['label']);
        $this->assertEquals([], $result['value']);
    }

    public function testToArrayWithIncompleteGeoCoordinates()
    {
        $geoCoordinates = Schema::geoCoordinates()
        ->latitude(null)
        ->longitude(18.0686)
        ->address('Stockholm, Sweden');

        $field  = new GeoCoordinatesField('location', 'Location', $geoCoordinates);
        $result = $field->toArray();

        $this->assertEquals('google_map', $result['type']);
        $this->assertEquals('location', $result['key']);
        $this->assertEquals('location', $result['name']);
        $this->assertEquals('Location', $result['label']);
        $this->assertEquals([], $result['value']);
    }
}
