<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\Services\NullSourceService;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class WpPostMetaFactoryOriginIdDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with the @id from the schemaObject as originId
     */
    public function testCreate()
    {
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('@id', 'foo');
        $factory = new WpPostMetaFactoryOriginIdDecorator($this->getInnerFactory());

        $result = $factory->create($schemaObject, new NullSourceService());

        $this->assertEquals('foo', $result['originId']);
    }

    private function getInnerFactory(): WpPostMetaFactoryInterface
    {
        return new class implements WpPostMetaFactoryInterface {
            public function create(BaseType $schemaObject, ISource $source): array
            {
                return [];
            }
        };
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
