<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class SchemaDataDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with schemaData key containing the schemaObject as an array
     */
    public function testCreate()
    {
        $schemaObject = Schema::thing()->setProperty('foo', 'bar');
        $factory      = new SchemaDataDecorator(new WpPostArgsFromSchemaObject());
        $result       = $factory->transform($schemaObject, new Source('', ''));

        $this->assertEquals($schemaObject->toArray(), $result['meta_input']['schemaData']);
    }

    /**
     * @testdox strips possible "id" key from schemaObject before adding it to the meta input
     */
    public function testCreateWithId()
    {
        $schemaObject = Schema::thing()->setProperty('foo', 'bar')->setProperty('id', '123');
        $factory      = new SchemaDataDecorator(new WpPostArgsFromSchemaObject());
        $result       = $factory->transform($schemaObject, new Source('', ''));

        $this->assertArrayNotHasKey('id', $result['meta_input']['schemaData']);
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
