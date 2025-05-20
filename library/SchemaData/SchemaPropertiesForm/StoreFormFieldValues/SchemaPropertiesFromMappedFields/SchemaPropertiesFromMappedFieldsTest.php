<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields;

use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use PHPUnit\Framework\MockObject\MockObject;

class SchemaPropertiesFromMappedFieldsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $schemaPropertiesFromMappedFields = new SchemaPropertiesFromMappedFields($this->getSchemaPropertiesWithParamTypesInterface(), []);
        $this->assertInstanceOf(SchemaPropertiesFromMappedFields::class, $schemaPropertiesFromMappedFields);
    }

    private function getSchemaPropertiesWithParamTypesInterface(): GetSchemaPropertiesWithParamTypesInterface|MockObject
    {
        return $this->createMock(GetSchemaPropertiesWithParamTypesInterface::class);
    }
}
