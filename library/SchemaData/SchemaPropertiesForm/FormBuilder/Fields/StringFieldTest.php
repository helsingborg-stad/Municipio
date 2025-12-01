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
        $this->assertEquals('test_name', $result['name']);
        $this->assertEquals('Test Label', $result['label']);
    }

    public function testToArrayHandlesNullValue()
    {
        $field  = new StringField('test_name', 'Test Label', null);
        $result = $field->toArray();

        $this->assertEquals('', $field->getValue());
    }
}
