<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\GeoCoordinates;

class GeoCoordinatesFromAcfGoogleMapsFieldSanitizerTest extends TestCase
{
    /**
     * @testdox Converts single ACF Google Maps field to postal address
     */
    public function testConvertsSingleToGeoCoordinates()
    {
        $sanitizer = new GeoCoordinatesFromAcfGoogleMapsFieldSanitizer();
        $result    = $sanitizer->sanitize($this->getValidMetaData(), ['GeoCoordinates']);

        $this->assertInstanceOf(GeoCoordinates::class, $result);
        $this->assertEquals(56.05794299999999, $result['latitude']);
        $this->assertEquals(12.805653, $result['longitude']);
        $this->assertEquals('Hjortsby Torp', $result['name']);
        $this->assertEquals('Hjortsby Torp, Hjortshögsvägen, Mörarp, Sverige', $result['address']);
        $this->assertEquals('253 54', $result['postalCode']);
        $this->assertEquals('Sverige', $result['addressCountry']);
    }

    /**
     * @testdox Converts array of ACF Google Maps fields to array of postal addresses
     */
    public function testConvertsArrayOfGeoCoordinates()
    {
        $sanitizer = new GeoCoordinatesFromAcfGoogleMapsFieldSanitizer();
        $addresses = [$this->getValidMetaData(), $this->getValidMetaData()];
        $result    = $sanitizer->sanitize($addresses, ['GeoCoordinates', 'GeoCoordinates[]']);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(GeoCoordinates::class, $result[0]);
        $this->assertInstanceOf(GeoCoordinates::class, $result[1]);
        $this->assertEquals('Hjortsby Torp', $result[0]['name']);
        $this->assertEquals('Hjortsby Torp', $result[1]['name']);
    }

    private function getValidMetaData(): array
    {
        return [
            'address'       => 'Hjortsby Torp, Hjortshögsvägen, Mörarp, Sverige',
            'lat'           => 56.05794299999999,
            'lng'           => 12.805653,
            'zoom'          => 11,
            'place_id'      => 'ChIJIzjbS0vNU0YR3eZNgjX5U-o',
            'name'          => 'Hjortsby Torp',
            'street_number' => '500',
            'street_name'   => 'Hjortshögsvägen',
            'city'          => 'Mörarp',
            'state'         => 'Skåne län',
            'post_code'     => '253 54',
            'country'       => 'Sverige',
            'country_short' => 'SE'
        ];
    }
}
