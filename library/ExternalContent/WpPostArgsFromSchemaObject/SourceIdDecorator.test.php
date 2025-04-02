<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;

class SourceIdDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with the sourceId from the source
     */
    public function testAppliesSourceId()
    {
        $schemaObject = $this->getSchemaObject();
        $factory      = new SourceIdDecorator('foo', new WpPostArgsFromSchemaObject());

        $result = $factory->transform($schemaObject);

        $this->assertEquals('foo', $result['meta_input']['sourceId']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
