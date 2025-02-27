<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Thing;
use WP_Query;

class SourceIdDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with the sourceId from the source
     */
    public function testAppliesSourceId()
    {
        $schemaObject = $this->getSchemaObject();
        $factory      = new SourceIdDecorator('foo', new WpPostFactory());

        $result = $factory->create($schemaObject);

        $this->assertEquals('foo', $result['meta_input']['sourceId']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
