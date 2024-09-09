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
        return new class implements SourceInterface {
            public function getId(): string
            {
                return 'foo';
            }
            public function getObject(string|int $id): ?BaseType
            {
                return new Thing();
            }
            public function getObjects(?WP_Query $query = null): array
            {
                return [];
            }
            public function getPostType(): string
            {
                return '';
            }
            public function getSchemaObjectType(): string
            {
                return '';
            }
        };
    }
}
