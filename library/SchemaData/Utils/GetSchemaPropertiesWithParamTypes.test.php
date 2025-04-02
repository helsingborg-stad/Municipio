<?php

namespace Municipio\SchemaData\Utils;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;

class GetSchemaPropertiesWithParamTypesTest extends TestCase
{
    public function testReturnsPropertiesAndTheirTypes()
    {

        $schemaObject                      = Schema::thing();
        $getSchemaPropertiesWithParamTypes = new GetSchemaPropertiesWithParamTypes();
        $actual                            = $getSchemaPropertiesWithParamTypes->getSchemaPropertiesWithParamTypes($schemaObject::class);

        $this->assertContains('string', $actual['additionalType']);
        $this->assertContains('ImageObject', $actual['image']);
    }
}
