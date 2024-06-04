<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use Exception;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\PostalAddress;

class PostalAddressFromAcfGoogleMapsFieldSanitizerTest extends TestCase
{
    public function testConvertsToPostalAddress()
    {
        $sanitizer = new PostalAddressFromAcfGoogleMapsFieldSanitizer();
        $result    = $sanitizer->sanitize($this->getValidMetaData(), ['PostalAddress']);

        $this->assertInstanceOf(PostalAddress::class, $result);
        $this->assertEquals('Hjortsby Torp', $result['name']);
        $this->assertEquals('500 Hjortshögsvägen', $result['streetAddress']);
        $this->assertEquals('Mörarp', $result['addressLocality']);
        $this->assertEquals('Skåne län', $result['addressRegion']);
        $this->assertEquals('253 54', $result['postalCode']);
        $this->assertEquals('Sverige', $result['addressCountry']);
        $this->assertEquals(56.05794299999999, $result['geo']['latitude']);
        $this->assertEquals(12.805653, $result['geo']['longitude']);
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
