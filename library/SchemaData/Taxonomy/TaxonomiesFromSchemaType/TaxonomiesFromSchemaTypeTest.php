<?php

declare(strict_types=1);


namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;
use WpService\Contracts\_x;
use WpService\Contracts\ApplyFilters;

class TaxonomiesFromSchemaTypeTest extends TestCase
{
    private TaxonomiesFromSchemaTypeInterface $instance;

    protected function setUp(): void
    {
        $this->instance = new TaxonomiesFromSchemaType(
            $this->getTaxonomyFactory(),
            $this->getSchemaToPostTypeResolver(),
            $this->getWpService(),
        );
    }

    #[TestDox('Returns an empty array for any unknown schema type')]
    public function testReturnsEmptyArrayForUnknownSchemaType(): void
    {
        $taxonomies = $this->instance->create('UnknownSchemaType');
        static::assertIsArray($taxonomies);
        static::assertEmpty($taxonomies);
    }

    #[TestDox('Returns array containing taxonomies for known schema types')]
    #[DataProvider('knownSchemaTypesProvider')]
    public function testReturnsTaxonomiesForKnownSchemaTypes(string $schemaType): void
    {
        // Assert array contains only instances of TaxonomyInterface
        static::assertTrue(
            $this->assertEachInArrayIsInstanceOf($this->instance->create($schemaType), TaxonomyInterface::class),
            sprintf('Taxonomies returned for schema type %s', $schemaType)
        );
    }

    public static function knownSchemaTypesProvider(): array
    {
        return [
            'JobPosting' => ['JobPosting'],
            'Event' => ['Event'],
            'Project' => ['Project'],
        ];
    }

    private function assertEachInArrayIsInstanceOf(array $array, string $class): bool
    {
        foreach ($array as $item) {
            if(!($item instanceof $class)) {
                return false;
            }
        }

        return true;
    }

    private function getTaxonomyFactory(): TaxonomyFactoryInterface|MockObject
    {
        return $this->createMock(TaxonomyFactoryInterface::class);
    }

    private function getSchemaToPostTypeResolver(): SchemaToPostTypeResolverInterface|MockObject
    {
        return $this->createMock(SchemaToPostTypeResolverInterface::class);
    }

    private function getWpService(): __|_x|ApplyFilters
    {
        return new class implements __, _x, ApplyFilters {
            public function __($text, ...$args): string
            {
                return $text;
            }

            // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
            public function _x($text, $context, ...$args): string
            {
                return $text;
            }

            public function applyFilters(string $tag, $value, ...$args): mixed
            {
                return $value;
            }
        };
    }
}
