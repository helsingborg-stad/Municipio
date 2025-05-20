<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\Schema;

class UrlHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $urlHandler = new UrlHandler();
        $this->assertInstanceOf(UrlHandler::class, $urlHandler);
    }
    /**
     * @testdox supports method returns true for valid URL
     */
    public function testSupportsMethodReturnsTrueForValidUrl(): void
    {
        $urlHandler    = new UrlHandler();
        $propertyName  = 'url';
        $fieldType     = 'url';
        $value         = 'https://example.com';
        $propertyTypes = ['string'];

        $this->assertTrue($urlHandler->supports($propertyName, $fieldType, $value, $propertyTypes));
    }
    /**
     * @testdox supports method returns false for invalid URL
     */
    public function testSupportsMethodReturnsFalseForInvalidUrl(): void
    {
        $urlHandler    = new UrlHandler();
        $propertyName  = 'url';
        $fieldType     = 'url';
        $value         = 'invalid-url';
        $propertyTypes = ['string'];

        $this->assertFalse($urlHandler->supports($propertyName, $fieldType, $value, $propertyTypes));
    }
    /**
     * @testdox handle method sets the URL property on the schema object
     */
    public function testHandleMethodSetsTheUrlPropertyOnTheSchemaObject(): void
    {
        $urlHandler   = new UrlHandler();
        $schemaObject = Schema::thing();
        $propertyName = 'url';
        $value        = 'https://example.com';

        $urlHandler->handle($schemaObject, $propertyName, $value);

        $this->assertEquals($value, $schemaObject->getProperty($propertyName));
    }
}
