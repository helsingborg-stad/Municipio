<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class DateTimeHandlerTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $handler = new DateTimeHandler();
        $this->assertInstanceOf(DateTimeHandler::class, $handler);
    }

    /**
     * @testdox supports() returns true for date_time_picker field type and valid DateTimeInterface value
     */
    public function testSupportsReturnsTrueForDateTimePickerFieldTypeAndValidDateTimeInterfaceValue(): void
    {
        $handler = new DateTimeHandler();
        $result  = $handler->supports('testProperty', 'date_time_picker', '2025-06-19', ['\DateTimeInterface']);

        $this->assertTrue($result);
    }

    /**
     * @testdox supports() returns false for invalid field types or values
     * @dataProvider supportsDataProvider
     */
    public function testSupportsReturnsFalseForInvalidFieldTypesOrValues(string $fieldType, mixed $value, array $propertyTypes): void
    {
        $handler = new DateTimeHandler();
        $result  = $handler->supports('testProperty', $fieldType, $value, $propertyTypes);

        $this->assertFalse($result);
    }

    public function supportsDataProvider(): array
    {
        return [
            ['text', '2025-06-19', ['\DateTimeInterface']],
            ['date_picker', '2025-06-19', ['\DateTimeInterface']],
            ['date_time_picker', 'invalid-date', ['\DateTimeInterface']],
            ['date_time_picker', '', ['\DateTimeInterface']],
            ['date_time_picker', '2025-06-19', ['string']],
            ['date_time_picker', '2025-06-19', []],
        ];
    }

    /**
     * @testdox handle() sets the property on the schema object
     */
    public function testHandleSetsPropertyOnSchemaObject(): void
    {
        $handler      = new DateTimeHandler();
        $schemaObject = Schema::event();
        $propertyName = 'startDate';

        $handler->handle($schemaObject, $propertyName, '2025-06-19');

        $this->assertInstanceOf(\DateTimeInterface::class, $schemaObject->getProperty($propertyName));
        $this->assertEquals('2025-06-19', $schemaObject->getProperty($propertyName)->format('Y-m-d'));
    }
}
