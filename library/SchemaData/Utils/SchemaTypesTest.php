<?php

namespace Municipio\SchemaData\Utils;

use PHPUnit\Framework\TestCase;

class SchemaTypesTest extends TestCase
{
    public function testClassCanBeInstantiated()
    {
        $schemaTypes = new \Municipio\SchemaData\Utils\SchemaTypes();
        $this->assertInstanceOf(\Municipio\SchemaData\Utils\SchemaTypes::class, $schemaTypes);
    }

    public function testGetSchemaTypesReturnsArrayOfSchemaTypes()
    {
        $schemaTypes = new \Municipio\SchemaData\Utils\SchemaTypes();
        $result      = $schemaTypes->getSchemaTypes();

        // Check for a number of random schema types
        $this->assertContains('Event', $result);
        $this->assertContains('Organization', $result);
        $this->assertContains('Person', $result);
        $this->assertContains('Place', $result);
    }
}
