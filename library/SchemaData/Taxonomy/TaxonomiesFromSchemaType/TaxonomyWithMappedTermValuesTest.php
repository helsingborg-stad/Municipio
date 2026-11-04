<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use PHPUnit\Framework\TestCase;

class TaxonomyWithMappedTermValuesTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(), ['true' => 'Free']);

        $this->assertInstanceOf(TaxonomyWithMappedTermValues::class, $decorator);
    }

    public function testDelegatesSimplePropertiesToInner(): void
    {
        $inner = new FakeTaxonomy(
            name: 'event_is_accessible_for_free',
            schemaType: 'Event',
            schemaProperty: 'isAccessibleForFree',
            objectTypes: ['event'],
            arguments: ['public' => true],
            label: 'Prices',
            singularLabel: 'Price',
        );

        $decorator = new TaxonomyWithMappedTermValues($inner, ['true' => 'Free']);

        $this->assertSame('event_is_accessible_for_free', $decorator->getName());
        $this->assertSame('Event', $decorator->getSchemaType());
        $this->assertSame('isAccessibleForFree', $decorator->getSchemaProperty());
        $this->assertSame(['event'], $decorator->getObjectTypes());
        $this->assertSame(['public' => true], $decorator->getArguments());
        $this->assertSame('Prices', $decorator->getLabel());
        $this->assertSame('Price', $decorator->getSingularLabel());
    }

    public function testUsesExplicitNameWhenGiven(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(name: 'event_is_accessible_for_free'), ['true' => 'Free'], 'event_price');

        $this->assertSame('event_price', $decorator->getName());
    }

    public function testMapsValueFoundInMap(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(formatTermValueReturn: 'true'), ['true' => 'Free']);

        $this->assertSame('Free', $decorator->formatTermValue('true', []));
    }

    public function testReturnsNullForValueMissingFromMap(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(formatTermValueReturn: 'false'), ['true' => 'Free']);

        $this->assertNull($decorator->formatTermValue('false', []));
    }

    public function testReturnsNullWhenInnerReturnsNull(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(formatTermValueReturn: null), ['true' => 'Free']);

        $this->assertNull($decorator->formatTermValue('true', []));
    }

    public function testMapsArrayValuesAndDropsUnmapped(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(formatTermValueReturn: ['a', 'b', 'c']), ['a' => 'A', 'c' => 'C']);

        $this->assertSame(['A', 'C'], $decorator->formatTermValue(['a', 'b', 'c'], []));
    }

    public function testReturnsNullWhenNoArrayValueIsMapped(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(formatTermValueReturn: ['b']), ['a' => 'A']);

        $this->assertNull($decorator->formatTermValue(['b'], []));
    }
}
