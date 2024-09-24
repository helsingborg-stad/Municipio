<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Thing;

class AddChecksumTest extends TestCase
{
    /**
     * @testdox Checksum is applied as meta input
     */
    public function testAppliesChecksum()
    {
        $schemaObject = new Thing();
        $factory      = new AddChecksum(new WpPostFactory());

        $result = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals('8f11aed0a9fa79ac707b8a4846a74f27', $result['meta_input']['checksum']);
    }
}
