<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class WpPostMetaFactoryTest extends TestCase
{
    /**
     * @testdox returns an array with schemaData key containing the schemaObject as an array
     */
    public function testCreate()
    {
        $schemaObject = $this->getSchemaObject();
        $factory      = new WpPostMetaFactory();

        $result = $factory->create($schemaObject);

        $this->assertEquals($schemaObject->toArray(), $result['schemaData']);
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
