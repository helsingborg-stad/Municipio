<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class SchemaDataDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with schemaData key containing the schemaObject as an array
     */
    public function testCreate()
    {
        $schemaObject = $this->getSchemaObject();
        $factory      = new SchemaDataDecorator(new WpPostFactory());
        $result       = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals($schemaObject->toArray(), $result['meta_input']['schemaData']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
            public function toArray(): array
            {
                return ['foo' => 'bar'];
            }
        };
    }
}
