<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;

class OriginIdDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with the @id from the schemaObject as originId
     */
    public function testCreate()
    {
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('@id', 'foo');
        $factory = new OriginIdDecorator(new WpPostArgsFromSchemaObject());

        $result = $factory->transform($schemaObject);

        $this->assertEquals('foo', $result['meta_input']['originId']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
