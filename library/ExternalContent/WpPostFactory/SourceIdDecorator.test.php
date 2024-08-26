<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Sources\Services\NullSource;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class SourceIdDecoratorTest extends TestCase
{
    /**
     * @testdox returns an array with the sourceId from the source
     */
    public function testAppliesSourceId()
    {
        $schemaObject = $this->getSchemaObject();
        $source       = $this->getSource();
        $factory      = new SourceIdDecorator(new WpPostFactory());

        $result = $factory->create($schemaObject, $source);

        $this->assertEquals('foo', $result['meta_input']['sourceId']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }

    private function getSource(): SourceInterface
    {
        return new class extends NullSource {
            public function getId(): string
            {
                return 'foo';
            }
        };
    }
}
