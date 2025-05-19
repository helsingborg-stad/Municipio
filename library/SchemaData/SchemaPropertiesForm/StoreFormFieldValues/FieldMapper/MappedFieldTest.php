<?php

namespace WPDesk\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper;

use PHPUnit\Framework\TestCase;

class MappedFieldTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $mappedField = new MappedField('testName', 'testType', 'testValue');
        $this->assertInstanceOf(MappedField::class, $mappedField);
    }
}
