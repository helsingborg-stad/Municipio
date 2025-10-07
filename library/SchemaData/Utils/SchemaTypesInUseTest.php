<?php

namespace Municipio\SchemaData\Utils;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use wpdb;

class SchemaTypesInUseTest extends TestCase
{
    public function testClassCanBeInstantiated()
    {
        $schemaTypesInUse = new SchemaTypesInUse($this->getWpdb());
        $this->assertInstanceOf(SchemaTypesInUse::class, $schemaTypesInUse);
    }

    public function testQueryIsCorrect()
    {
        $wpdb  = $this->getWpdb();
        $query = "SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE %s";
        $wpdb->expects($this->once())->method('prepare')->with($query, SchemaTypesInUse::SCHEMA_TYPE_OPTION_NAME)->willReturn('SELECT option_value FROM wp_options WHERE option_name LIKE %s');
        $wpdb->method('get_col')->willReturn(['Event']);
        $schemaTypesInUse = new SchemaTypesInUse($wpdb);

        $this->assertEquals(['Event'], $schemaTypesInUse->getSchemaTypesInUse());
    }

    private function getWpdb(): wpdb|MockObject
    {
        return $this->createMock(wpdb::class);
    }
}
