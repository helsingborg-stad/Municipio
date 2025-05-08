<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use PHPUnit\Framework\TestCase;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;

class StringFieldTest extends TestCase
{
    public function testToArrayReturnsCorrectStructure()
    {
        $field  = new StringField('test_name', 'Test Label', 'Test Value');
        $result = $field->toArray();

        $this->assertIsArray($result);
        $this->assertEquals('text', $result['type']);
        $this->assertEquals('test_name', $result['key']);
        $this->assertEquals('test_name', $result['name']);
        $this->assertEquals('Test Label', $result['label']);
        $this->assertEquals('Test Value', $result['value']);
    }

    public function testToArrayHandlesNullValue()
    {
        $field  = new StringField('test_name', 'Test Label', null);
        $result = $field->toArray();

        $this->assertEquals('', $result['value']);
    }

    public function testSanitizeValueHandlesString()
    {
        $reflection = new \ReflectionClass(StringField::class);
        $method     = $reflection->getMethod('sanitizeValue');
        $method->setAccessible(true);

        $field  = new StringField('test_name', 'Test Label');
        $result = $method->invokeArgs($field, ['Valid String']);

        $this->assertEquals('Valid String', $result);
    }

    public function testSanitizeValueHandlesNull()
    {
        $reflection = new \ReflectionClass(StringField::class);
        $method     = $reflection->getMethod('sanitizeValue');
        $method->setAccessible(true);

        $field  = new StringField('test_name', 'Test Label');
        $result = $method->invokeArgs($field, [null]);

        $this->assertEquals('', $result);
    }
}
