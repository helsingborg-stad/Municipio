<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\Thing;

class AddChecksumTest extends TestCase
{
    /**
     * @testdox Checksum is applied as meta input
     */
    public function testAppliesChecksum()
    {
        $schemaObject = new Thing();
        $factory      = new AddChecksum($this->getInnerFactory());

        $result = $factory->transform($schemaObject);

        $this->assertEquals('8f11aed0a9fa79ac707b8a4846a74f27', $result['meta_input']['checksum']);
    }

    private function getInnerFactory(): WpPostArgsFromSchemaObjectInterface
    {
        return new class implements WpPostArgsFromSchemaObjectInterface {
            public function transform(\Municipio\Schema\BaseType $schemaObject): array
            {
                return [
                    'post_title'   => '',
                    'post_content' => '',
                    'post_status'  => 'publish',
                    'post_type'    => '',
                    'meta_input'   => []
                ];
            }
        };
    }
}
