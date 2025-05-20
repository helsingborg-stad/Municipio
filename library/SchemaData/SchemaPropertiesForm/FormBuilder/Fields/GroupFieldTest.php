<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use PHPUnit\Framework\TestCase;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GroupField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldInterface;

class GroupFieldTest extends TestCase
{
    public function testRenderSubFieldsReturnsCorrectSubFields()
    {
        // Mock subfields
        $mockSubField1 = $this->createMock(FieldInterface::class);
        $mockSubField1->method('toArray')->willReturn(['key' => 'subfield1', 'name' => 'SubField 1']);

        $mockSubField2 = $this->createMock(FieldInterface::class);
        $mockSubField2->method('toArray')->willReturn(['key' => 'subfield2', 'name' => 'SubField 2']);

        // Create GroupField instance
        $groupField = new GroupField('group1', 'Group 1', [$mockSubField1, $mockSubField2]);

        // Reflection to access private method
        $reflection = new \ReflectionClass(GroupField::class);
        $method     = $reflection->getMethod('renderSubFields');
        $method->setAccessible(true);

        // Expected result
        $expected = [
            ['key' => 'subfield1', 'name' => 'SubField 1'],
            ['key' => 'subfield2', 'name' => 'SubField 2'],
        ];

        // Assert
        $this->assertEquals($expected, $method->invoke($groupField));
    }
}
