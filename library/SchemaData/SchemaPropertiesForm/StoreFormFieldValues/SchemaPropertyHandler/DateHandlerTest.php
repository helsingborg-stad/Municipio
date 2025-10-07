<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class DateHandlerTest extends TestCase
{
    public function testClassCanBeInstantiated(): void
    {
        $handler = new DateHandler();
        $this->assertInstanceOf(DateHandler::class, $handler);
    }

    /**
     * @testdox supports() returns true for date_picker field type and valid DateTimeInterface value
     */
    public function testSupportsReturnsTrueForDatePickerFieldTypeAndValidDateTimeInterfaceValue(): void
    {
        $handler = new DateHandler();
        $result  = $handler->supports('testProperty', 'date_picker', '2025-06-19', ['\DateTimeInterface']);

        $this->assertTrue($result);
    }

    /**
     * @testdox supports() returns false for invalid field types or values
     * @dataProvider supportsDataProvider
     */
    public function testSupportsReturnsFalseForInvalidFieldTypesOrValues(string $fieldType, mixed $value, array $propertyTypes): void
    {
        $handler = new DateHandler();
        $result  = $handler->supports('testProperty', $fieldType, $value, $propertyTypes);

        $this->assertFalse($result);
    }

    public static function supportsDataProvider(): array
    {
        return [
            ['text', '2025-06-19', ['\DateTimeInterface']],
            ['date_time_picker', '2025-06-19', ['\DateTimeInterface']],
            ['date_picker', 'invalid-date', ['\DateTimeInterface']],
            ['date_picker', '', ['\DateTimeInterface']],
            ['date_picker', '2025-06-19', ['string']],
            ['date_picker', '2025-06-19', []],
        ];
    }

    /**
     * @testdox handle() sets the property on the schema object
     */
    public function testHandleSetsPropertyOnSchemaObject(): void
    {
        $handler      = new DateHandler();
        $schemaObject = Schema::event();

        $handler->handle($schemaObject, 'startDate', '2025-06-19');

        $this->assertInstanceOf(\DateTimeInterface::class, $schemaObject->getProperty('startDate'));
        $this->assertEquals('2025-06-19', $schemaObject->getProperty('startDate')->format('Y-m-d'));
    }
}
