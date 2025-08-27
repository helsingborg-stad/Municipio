<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use PHPUnit\Framework\TestCase;

class MultiSelectHandlerTest extends TestCase
{
    private MultiSelectHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new MultiSelectHandler();
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(MultiSelectHandler::class, $this->handler);
    }

    /**
     * @testdox supports method returns false for unsupported field type
     */
    public function testSupportsReturnsFalseForUnsupportedFieldType()
    {
        $this->assertFalse($this->handler->supports('', 'text', ['testValue'], []));
    }

    /**
     * @testdox supports method returns false if value is not an array of strings
     */
    public function testSupportsReturnsFalseForNonArrayOfStrings()
    {
        $this->assertFalse($this->handler->supports('testProperty', 'select', ['value1', 2], []));
    }

    /**
     * @testdox supports method returns false if value is not an array
     */
    public function testSupportsReturnsFalseForNonArrayValue()
    {
        $this->assertFalse($this->handler->supports('testProperty', 'select', 'notAnArray', []));
    }

    /**
     * @testdox supports method returns true if field type is 'select' and value is an array of strings
     */
    public function testSupportsReturnsTrueForSupportedFieldType()
    {
        $this->assertTrue($this->handler->supports('testProperty', 'select', ['value1', 'value2'], []));
    }
}
