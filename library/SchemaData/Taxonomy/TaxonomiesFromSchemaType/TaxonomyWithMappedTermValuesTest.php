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

    public function testUsesInnerNameWhenNoNameGiven(): void
    {
        $decorator = new TaxonomyWithMappedTermValues(new FakeTaxonomy(name: 'event_is_accessible_for_free'), ['true' => 'Free']);

        $this->assertSame('event_is_accessible_for_free', $decorator->getName());
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
