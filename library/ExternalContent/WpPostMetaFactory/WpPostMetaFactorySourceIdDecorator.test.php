<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\Services\NullSourceService;
use Municipio\ExternalContent\Sources\Services\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class WpPostMetaFactorySourceIdDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with the sourceId from the source
     */
    public function testAppliesSourceId()
    {
        $schemaObject = $this->getSchemaObject();
        $source       = $this->getSource();
        $factory      = new WpPostMetaFactorySourceIdDecorator(new WpPostMetaFactory());

        $result = $factory->create($schemaObject, $source);

        $this->assertEquals('foo', $result['sourceId']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }

    private function getSource(): ISource
    {
        return new class extends Source {
            public function __construct()
            {
            }
            public function getId(): string
            {
                return 'foo';
            }
        };
    }
}
