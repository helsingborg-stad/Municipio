<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use PHPUnit\Framework\TestCase;

class TaxonomyDecoratorTest extends TestCase
{
    public function testDelegatesEverythingToInner(): void
    {
        $inner = new FakeTaxonomy(
            name: 'event_keywords_name',
            schemaType: 'Event',
            schemaProperty: 'keywords.name',
            objectTypes: ['event'],
            arguments: ['public' => true],
            label: 'Event Tags',
            singularLabel: 'Event Tag',
            formatTermValueReturn: 'Konst',
        );

        $decorator = new class ($inner) extends TaxonomyDecorator {
        };

        $this->assertSame('event_keywords_name', $decorator->getName());
        $this->assertSame('Event', $decorator->getSchemaType());
        $this->assertSame('keywords.name', $decorator->getSchemaProperty());
        $this->assertSame(['event'], $decorator->getObjectTypes());
        $this->assertSame(['public' => true], $decorator->getArguments());
        $this->assertSame('Event Tags', $decorator->getLabel());
        $this->assertSame('Event Tag', $decorator->getSingularLabel());
        $this->assertSame('Konst', $decorator->formatTermValue('Konst', []));
    }
}
