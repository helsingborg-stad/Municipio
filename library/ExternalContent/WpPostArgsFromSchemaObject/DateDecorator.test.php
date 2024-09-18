<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class DateDecoratorTest extends TestCase
{
    /**
     * @testdox Sets post_date and post_modified to datePublished and dateModified from schemaObject
     */
    public function testCreate()
    {
        $schemaObject = $this->getBaseTypeInstance([
            'datePublished' => '2021-01-01',
            'dateModified'  => '2021-01-02',
        ]);

        $inner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $inner->method('create')->willReturn([]);

        $wpPostFactory = new DateDecorator($inner);
        $wpPost        = $wpPostFactory->create($schemaObject, new Source('', ''));

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
