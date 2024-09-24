<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Thing;

class ChecksumDecoratorTest extends TestCase
{
    /**
     * @testdox Checksum is applied as meta input
     */
    public function testAppliesChecksum()
    {
        $schemaObject = new Thing();
        $factory      = new ChecksumDecorator(new WpPostFactory());

        $result = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals('a926b0ac10295f7e1461d6ac6ce34a4b78c148b3', $result['meta_input']['checksum']);
    }
}
