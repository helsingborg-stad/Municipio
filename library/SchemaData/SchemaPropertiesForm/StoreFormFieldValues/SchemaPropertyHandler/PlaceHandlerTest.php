<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\Place;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class PlaceHandlerTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $placeHandler = new PlaceHandler();
        $this->assertInstanceOf(PlaceHandler::class, $placeHandler);
    }

    /**
     * @testdox supports method returns true for valid place value
     */
    public function testSupportsMethodReturnsTrueForValidPlaceValue(): void
    {
        $placeHandler = new PlaceHandler();
        $propertyName = 'place';
        $fieldType    = 'google_map';
        $value        = [
            'lat'     => 59.3293,
            'lng'     => 18.0686,
            'address' => 'Stockholm, Sweden',
        ];

        $this->assertTrue($placeHandler->supports($propertyName, $fieldType, $value, ['Place']));
    }

    /**
     * @testdox supports method returns false for invalid place value
     */
    public function testSupportsMethodReturnsFalseForInvalidPlaceValue(): void
    {
        $placeHandler = new PlaceHandler();
        $propertyName = 'place';
        $fieldType    = 'google_map';
        $value        = [
            'lat' => 59.3293,
            'lng' => 18.0686,
        ];

        $this->assertFalse($placeHandler->supports($propertyName, $fieldType, $value, ['Place']));
    }

    /**
     * @testdox handle method sets the place property on the schema object
     */
    public function testHandleMethodSetsThePlacePropertyOnTheSchemaObject(): void
    {
        $placeHandler = new PlaceHandler();
        $schemaObject = Schema::event();
        $propertyName = 'location';
        $value        = [
            'lat'     => 59.3293,
            'lng'     => 18.0686,
            'address' => 'Stockholm, Sweden',
        ];

        $updatedSchemaObject = $placeHandler->handle($schemaObject, $propertyName, $value);

        // Assert that the property was set correctly.
        /** @var Place|null */
        $place = $updatedSchemaObject->getProperty($propertyName);
        if ($place) {
            self::assertEquals(59.3293, $place->getProperty('latitude'));
            self::assertEquals(18.0686, $place->getProperty('longitude'));
            self::assertEquals('Stockholm, Sweden', $place->getProperty('address'));
        }
    }
}
