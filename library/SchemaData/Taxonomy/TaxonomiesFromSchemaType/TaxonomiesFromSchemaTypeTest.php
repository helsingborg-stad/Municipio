<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;
use WpService\Contracts\_x;

class TaxonomiesFromSchemaTypeTest extends TestCase
{
    private TaxonomiesFromSchemaTypeInterface $instance;

    protected function setUp(): void
    {
        $this->instance = new TaxonomiesFromSchemaType(
            $this->getTaxonomyFactory(),
            $this->getSchemaToPostTypeResolver(),
            $this->getWpService()
        );
    }

    /**
     * @testdox Returns an empty array for any unknown schema type
     */
    public function testReturnsEmptyArrayForUnknownSchemaType(): void
    {
        $taxonomies = $this->instance->create('UnknownSchemaType');
        $this->assertIsArray($taxonomies);
        $this->assertEmpty($taxonomies);
    }

    /**
     * @testdox Returns array containing taxonomies for known schema types
     * @dataProvider knownSchemaTypesProvider
     */
    public function testReturnsTaxonomiesForKnownSchemaTypes(string $schemaType): void
    {
        // Assert array contains only instances of TaxonomyInterface
        $this->assertEachInArrayIsInstanceOf(
            $this->instance->create($schemaType),
            TaxonomyInterface::class
        );
    }

    public function knownSchemaTypesProvider(): array
    {
        return [
            'JobPosting' => ['JobPosting'],
            'Event'      => ['Event'],
            'Project'    => ['Project'],
        ];
    }

    private function assertEachInArrayIsInstanceOf(array $array, string $class): void
    {
        foreach ($array as $item) {
            $this->assertInstanceOf($class, $item);
        }
    }

    private function getTaxonomyFactory(): TaxonomyFactoryInterface|MockObject
    {
        return $this->createMock(TaxonomyFactoryInterface::class);
    }

    private function getSchemaToPostTypeResolver(): SchemaToPostTypeResolverInterface|MockObject
    {
        return $this->createMock(SchemaToPostTypeResolverInterface::class);
    }

    private function getWpService(): __|_x
    {
        return new class implements __, _x {
            public function __($text, ...$args): string
            {
                return $text;
            }
            public function _x($text, $context, ...$args): string
            {
                return $text;
            }
        };
    }
}
