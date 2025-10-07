<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RequiredFieldTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $requiredField = new RequiredField($this->createMockField());

        $this->assertInstanceOf(RequiredField::class, $requiredField);
    }

    #[TestDox('toArray returns field settings with required flag')]
    public function testToArrayReturnsFieldSettingsWithRequiredFlag()
    {
        $field = $this->createMockField();
        $field->method('toArray')->willReturn([
            'type' => 'testType',
            'name' => 'testName',
        ]);

        $requiredField = new RequiredField($field);

        $this->assertEquals([
            'type'     => 'testType',
            'name'     => 'testName',
            'required' => true,
        ], $requiredField->toArray());
    }

    private function createMockField(): FieldInterface|MockObject
    {
        return $this->createMock(FieldInterface::class);
    }
}
