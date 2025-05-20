<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class TextHandlerTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $textHandler = new TextHandler();
        $this->assertInstanceOf(TextHandler::class, $textHandler);
    }

    /**
     * @testdox supports method returns true for valid text and string field type
     */
    public function testSupportsMethodReturnsTrueForValidText(): void
    {
        $textHandler  = new TextHandler();
        $propertyName = 'text';
        $fieldType    = 'text';
        $value        = 'This is a test';

        $this->assertTrue($textHandler->supports($propertyName, $fieldType, $value, ['string']));
    }

    /**
     * @testdox supports method returns false for invalid text
     */
    public function testSupportsMethodReturnsFalseForInvalidText(): void
    {
        $textHandler  = new TextHandler();
        $propertyName = 'text';
        $fieldType    = 'text';
        $value        = 12345;

        $this->assertFalse($textHandler->supports($propertyName, $fieldType, $value, ['string']));
    }

    /**
     * @testdox handle method sets the text property on the schema object
     */
    public function testHandleMethodSetsTextPropertyOnSchemaObject(): void
    {
        $schemaObject = Schema::thing();

        $textHandler = new TextHandler();
        $textHandler->handle($schemaObject, 'name', 'Test Schema');

        $this->assertEquals('Test Schema', $schemaObject->getProperty('name'));
    }
}
