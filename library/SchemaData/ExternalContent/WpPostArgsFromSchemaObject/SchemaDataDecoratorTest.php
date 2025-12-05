<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class SchemaDataDecoratorTest extends TestCase
{
    #[TestDox('returns an array with schemaData key containing the schemaObject as an array')]
    public function testCreate()
    {
        $schemaObject = Schema::thing()->setProperty('foo', 'bar');
        $factory      = new SchemaDataDecorator(new WpPostArgsFromSchemaObject());
        $result       = $factory->transform($schemaObject);

        $this->assertEquals($schemaObject->toArray(), $result['meta_input']['schemaData']);
    }

    #[TestDox('strips possible "id" key from schemaObject before adding it to the meta input')]
    public function testCreateWithId()
    {
        $schemaObject = Schema::thing()->setProperty('foo', 'bar')->setProperty('id', '123');
        $factory      = new SchemaDataDecorator(new WpPostArgsFromSchemaObject());
        $result       = $factory->transform($schemaObject);

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
