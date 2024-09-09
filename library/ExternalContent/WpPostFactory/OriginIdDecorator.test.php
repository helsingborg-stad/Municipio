<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class OriginIdDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with the @id from the schemaObject as originId
     */
    public function testCreate()
    {
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('@id', 'foo');
        $factory = new OriginIdDecorator(new WpPostFactory());

        $result = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals('foo', $result['meta_input']['originId']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
