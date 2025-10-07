<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\GeoCoordinates;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class GeoCoordinatesHandlerTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $geoCoordinatesHandler = new GeoCoordinatesHandler();
        $this->assertInstanceOf(GeoCoordinatesHandler::class, $geoCoordinatesHandler);
    }

    #[TestDox('supports method returns true for valid place value')]
    public function testSupportsMethodReturnsTrueForValidPlaceValue(): void
    {
        $geoCoordinatesHandler = new GeoCoordinatesHandler();
        $propertyName          = 'place';
        $fieldType             = 'google_map';
        $value                 = [
            'lat'     => 59.3293,
            'lng'     => 18.0686,
            'address' => 'Stockholm, Sweden',
        ];

        $this->assertTrue($geoCoordinatesHandler->supports($propertyName, $fieldType, $value, ['GeoCoordinates']));
    }

    #[TestDox('supports method returns false for invalid place value')]
    public function testSupportsMethodReturnsFalseForInvalidPlaceValue(): void
    {
        $geoCoordinatesHandler = new GeoCoordinatesHandler();
        $propertyName          = 'geo';
        $fieldType             = 'google_map';
        $value                 = [
            'lat' => 59.3293,
            'lng' => 18.0686,
        ];

        $this->assertFalse($geoCoordinatesHandler->supports($propertyName, $fieldType, $value, ['Place']));
    }

    #[TestDox('handle method sets the place property on the schema object')]
    public function testHandleMethodSetsThePlacePropertyOnTheSchemaObject(): void
    {
        $geoCoordinatesHandler = new GeoCoordinatesHandler();
        $schemaObject          = Schema::place();
        $propertyName          = 'geo';
        $value                 = [
            'lat'     => 59.3293,
            'lng'     => 18.0686,
            'address' => 'Stockholm, Sweden',
        ];

        $updatedSchemaObject = $geoCoordinatesHandler->handle($schemaObject, $propertyName, $value);

        $this->assertInstanceOf(GeoCoordinates::class, $updatedSchemaObject->getProperty($propertyName));
        $this->assertEquals(59.3293, $updatedSchemaObject->getProperty($propertyName)->getProperty('latitude'));
        $this->assertEquals(18.0686, $updatedSchemaObject->getProperty($propertyName)->getProperty('longitude'));
        $this->assertEquals('Stockholm, Sweden', $updatedSchemaObject->getProperty($propertyName)->getProperty('address'));
    }
}
