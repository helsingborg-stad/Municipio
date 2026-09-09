<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use PHPUnit\Framework\TestCase;

class TaxonomyFilteredBySubPropertyTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $decorator = new TaxonomyFilteredBySubProperty(
            new FakeTaxonomy(),
            'keywords',
            'inDefinedTermSet.name',
            'event_status',
        );

        $this->assertInstanceOf(TaxonomyFilteredBySubProperty::class, $decorator);
    }

    public function testDelegatesSimplePropertiesToInner(): void
    {
        $inner = new FakeTaxonomy(
            name: 'exhibition_event_keywords_name',
            schemaType: 'ExhibitionEvent',
            schemaProperty: 'keywords.name',
            objectTypes: ['exhibition_event'],
            arguments: ['public' => true],
            label: 'Event Statuses',
            singularLabel: 'Event Status',
        );

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_status');

        $this->assertSame('ExhibitionEvent', $decorator->getSchemaType());
        $this->assertSame('keywords.name', $decorator->getSchemaProperty());
        $this->assertSame(['exhibition_event'], $decorator->getObjectTypes());
        $this->assertSame(['public' => true], $decorator->getArguments());
        $this->assertSame('Event Statuses', $decorator->getLabel());
        $this->assertSame('Event Status', $decorator->getSingularLabel());
    }

    public function testDerivesNameUniquePerExpectedValueWhenNoNameGiven(): void
    {
        $inner = new FakeTaxonomy(name: 'exhibition_event');

        $statusDecorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_status');
        $otherDecorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_category');

        $this->assertNotSame($statusDecorator->getName(), $otherDecorator->getName());
        $this->assertSame('exhibition_event_event_status', $statusDecorator->getName());
        $this->assertSame('exhibition_event_event_category', $otherDecorator->getName());
    }

    public function testDerivedNameIsTruncatedToWordpressTaxonomyNameLimit(): void
    {
        $inner = new FakeTaxonomy(name: str_repeat('a', 32));

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_status');

        $this->assertLessThanOrEqual(32, strlen($decorator->getName()));
        $this->assertStringEndsWith('_event_status', $decorator->getName());
    }

    public function testUsesExplicitNameWhenGiven(): void
    {
        $inner = new FakeTaxonomy(name: 'exhibition_event_keywords_name');

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_status', 'exhibition_event_status');

        $this->assertSame('exhibition_event_status', $decorator->getName());
    }

    public function testKeepsValueWhenSubPropertyMatchesExpectedValue(): void
    {
        $inner = new FakeTaxonomy(formatTermValueReturn: 'Avslutad');

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_status');

        $result = $decorator->formatTermValue('Avslutad', $this->getExhibitionEventSchema());

        $this->assertSame('Avslutad', $result);
    }

    public function testFiltersOutValueWhenSubPropertyDoesNotMatchExpectedValue(): void
    {
        $inner = new FakeTaxonomy(formatTermValueReturn: 'Avslutad');

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'some_other_term_set');

        $result = $decorator->formatTermValue('Avslutad', $this->getExhibitionEventSchema());

        $this->assertNull($result);
    }

    public function testFiltersOutValueWhenInnerReturnsNull(): void
    {
        $inner = new FakeTaxonomy(formatTermValueReturn: null);

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_status');

        $result = $decorator->formatTermValue('Avslutad', $this->getExhibitionEventSchema());

        $this->assertNull($result);
    }

    public function testFiltersArrayValueToOnlyMatchingNames(): void
    {
        $inner = new FakeTaxonomy(formatTermValueReturn: ['Avslutad', 'Pågående']);

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'keywords', 'inDefinedTermSet.name', 'event_status');

        $schema = $this->getExhibitionEventSchema();
        $schema['keywords'][] = [
            '@type'            => 'DefinedTerm',
            'inDefinedTermSet' => ['@type' => 'DefinedTermSet', 'name' => 'other_set'],
            'name'             => 'Pågående',
        ];

        $result = $decorator->formatTermValue(['Avslutad', 'Pågående'], $schema);

        $this->assertSame(['Avslutad'], $result);
    }

    public function testReturnsNullWhenItemsPropertyPathIsMissing(): void
    {
        $inner = new FakeTaxonomy(formatTermValueReturn: 'Avslutad');

        $decorator = new TaxonomyFilteredBySubProperty($inner, 'missingProperty', 'inDefinedTermSet.name', 'event_status');

        $result = $decorator->formatTermValue('Avslutad', $this->getExhibitionEventSchema());

        $this->assertNull($result);
    }

    private function getExhibitionEventSchema(): array
    {
        return [
            '@type'    => 'ExhibitionEvent',
            'keywords' => [
                [
                    '@type'            => 'DefinedTerm',
                    'inDefinedTermSet' => [
                        '@type' => 'DefinedTermSet',
                        'name'  => 'event_status',
                    ],
                    'name'             => 'Avslutad',
                ],
            ],
        ];
    }
}

/**
 * Fake implementation of TaxonomyInterface for use in tests.
 */
class FakeTaxonomy implements TaxonomyInterface
{
    public function __construct(
        private string $name = 'fake_taxonomy',
        private string $schemaType = 'FakeSchemaType',
        private string $schemaProperty = 'fakeProperty',
        private array $objectTypes = ['post'],
        private array $arguments = [],
        private string $label = 'Fake Taxonomy',
        private string $singularLabel = 'Fake Taxonomy',
        private string|array|null $formatTermValueReturn = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSchemaType(): string
    {
        return $this->schemaType;
    }

    public function getSchemaProperty(): string
    {
        return $this->schemaProperty;
    }

    public function getObjectTypes(): array
    {
        return $this->objectTypes;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSingularLabel(): string
    {
        return $this->singularLabel;
    }

    public function formatTermValue(mixed $value, array $schema): string|array|null
    {
        return $this->formatTermValueReturn;
    }
}
