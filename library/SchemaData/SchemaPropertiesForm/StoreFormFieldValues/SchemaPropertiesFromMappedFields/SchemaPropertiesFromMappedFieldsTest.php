<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields;

class SchemaPropertiesFromMappedFieldsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $schemaPropertiesFromMappedFields = new SchemaPropertiesFromMappedFields();
        $this->assertInstanceOf(SchemaPropertiesFromMappedFields::class, $schemaPropertiesFromMappedFields);
    }
}
