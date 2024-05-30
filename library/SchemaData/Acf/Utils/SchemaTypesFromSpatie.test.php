<?php

namespace Municipio\SchemaData\Acf\Utils;

use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Schema;

class SchemaTypesFromSpatieTest extends TestCase
{
    public function testContainsRandomSchemaTypes()
    {
        $schemaTypes = new SchemaTypesFromSpatie();
        $types       = $schemaTypes->getSchemaTypes();
        $this->assertContains('CreativeWork', $types);
        $this->assertContains('Event', $types);
        $this->assertContains('Organization', $types);
        $this->assertContains('Person', $types);
        $this->assertContains('Place', $types);
        $this->assertContains('Product', $types);
        $this->assertContains('Review', $types);
        $this->assertContains('Thing', $types);
    }
}
