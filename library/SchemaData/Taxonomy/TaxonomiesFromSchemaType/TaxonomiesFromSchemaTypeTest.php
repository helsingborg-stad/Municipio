<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Taxonomy\TermFactory;
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
        $this->assertIsArray($taxonomies);
        $this->assertEmpty($taxonomies);
    }

    #[TestDox('Returns array containing taxonomies for known schema types')]
    #[DataProvider('knownSchemaTypesProvider')]
    public function testReturnsTaxonomiesForKnownSchemaTypes(string $schemaType): void
    {
        // Assert array contains only instances of TaxonomyInterface
        $array = $this->instance->create($schemaType);
        $class = TaxonomyInterface::class;

        if(count($array) === 0) {
            $this->fail(sprintf('Expected non-empty array for schema type %s', $schemaType));
        }
        
        foreach ($array as $item) {
            if (!($item instanceof $class)) {
                $this->fail(sprintf('Expected instance of %s, got %s', $class, get_class($item)));
            }
        }

        $this->assertTrue(true, sprintf('All items in the array are instances of %s', $class));
    }

    #[TestDox('applies filter "Municipio/Schema/Taxonomy/{schemaType}" to the taxonomies array')]
    public function testAppliesFilterToTaxonomiesArray(): void
    {
        $wpService = new class implements __, _x, ApplyFilters {
            public array $appliedFilters = [];
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
                $this->appliedFilters[] = ['tag' => $tag, 'value' => $value, 'args' => $args];
                return $value;
            }
        };

        $instance = new TaxonomiesFromSchemaType( $this->getTaxonomyFactory(), $this->getSchemaToPostTypeResolver(), $wpService );
        $instance->create('Event');
        
        static::assertNotEmpty($wpService->appliedFilters, 'Expected applyFilters to be called at least once');
        static::assertEquals('Municipio/Schema/Taxonomy/Event', $wpService->appliedFilters[0]['tag'], 'Expected filter tag to match schema type');
    }

    #[TestDox('splits Event keywords into audience and category taxonomies and maps isAccessibleForFree to a price taxonomy')]
    #[DataProvider('eventCategoryTermSetNamesProvider')]
    public function testEventTaxonomiesCreateExpectedTerms(string $categorySet): void
    {
        $instance = new TaxonomiesFromSchemaType(new TaxonomyFactory(), $this->getSchemaToPostTypeResolver(), $this->getWpService());
        $schema = [
            '@type' => 'Event',
            'isAccessibleForFree' => true,
            'keywords' => [
                $this->definedTerm('Barn', 'audience'),
                $this->definedTerm('Konst', $categorySet),
                $this->definedTerm('sommarlov', 'keyword'),
                // Legacy event API organizer groups must not be treated as audiences.
                $this->definedTerm('Helsingborgs Konserthus', 'user_groups'),
            ],
        ];

        $termNamesByTaxonomy = [];
        foreach ($instance->create('Event') as $taxonomy) {
            $terms = (new TermFactory())->create($taxonomy, $schema);
            $termNamesByTaxonomy[$taxonomy->getName()] = array_map(fn($term) => $term->name, $terms);
        }

        static::assertSame(['Barn'], $termNamesByTaxonomy['event_audience']);
        static::assertSame(['Konst'], $termNamesByTaxonomy['event_category']);
        static::assertSame(['Free'], $termNamesByTaxonomy['event_price']);
    }

    #[TestDox('does not add a price term to Event when it is not free')]
    public function testEventPriceTaxonomyIsEmptyWhenNotFree(): void
    {
        $instance = new TaxonomiesFromSchemaType(new TaxonomyFactory(), $this->getSchemaToPostTypeResolver(), $this->getWpService());
        $priceTaxonomy = array_values(array_filter($instance->create('Event'), fn($taxonomy) => $taxonomy->getName() === 'event_price'))[0];

        static::assertSame([], (new TermFactory())->create($priceTaxonomy, ['@type' => 'Event', 'isAccessibleForFree' => false]));
    }

    public static function eventCategoryTermSetNamesProvider(): array
    {
        return [
            'headless event API' => ['category'],
            'legacy event API' => ['event_categories'],
        ];
    }

    private function definedTerm(string $name, string $termSetName): array
    {
        return [
            '@type' => 'DefinedTerm',
            'name' => $name,
            'inDefinedTermSet' => ['@type' => 'DefinedTermSet', 'name' => $termSetName],
        ];
    }

    public static function knownSchemaTypesProvider(): array
    {
        return [
            'JobPosting' => ['JobPosting'],
            'Event' => ['Event'],
            'Project' => ['Project'],
            'ExhibitionEvent' => ['ExhibitionEvent'],
            'ElementarySchool' => ['ElementarySchool'],
            'Preschool' => ['Preschool'],
        ];
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
