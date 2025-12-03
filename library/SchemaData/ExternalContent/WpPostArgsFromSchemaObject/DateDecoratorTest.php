<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\SchemaData\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;

class DateDecoratorTest extends TestCase
{
    #[TestDox('Sets post_date and post_modified to datePublished and dateModified from schemaObject')]
    public function testCreate()
    {
        $schemaObject = $this->getBaseTypeInstance([
            'datePublished' => '2021-01-01',
            'dateModified'  => '2021-01-02',
        ]);

        $inner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $inner->method('transform')->willReturn([]);

        $wpPostFactory = new DateDecorator($inner);
        $wpPost        = $wpPostFactory->transform($schemaObject);

        $this->assertEquals('2021-01-01', $wpPost['post_date']);
        $this->assertEquals('2021-01-02', $wpPost['post_modified']);
    }


    private function getBaseTypeInstance(array $properties = []): BaseType
    {
        return new class ($properties) extends BaseType {
            public function __construct(array $properties)
            {
                $this->properties = $properties;
            }
        };
    }
}
