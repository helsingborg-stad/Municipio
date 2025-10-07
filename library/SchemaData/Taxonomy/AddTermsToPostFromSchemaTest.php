<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactoryInterface;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class AddTermsToPostFromSchemaTest extends TestCase
{
    #[TestDox('attaches to updated_postmeta action')]
    public function testCanBeInstantiated(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $instance  = new AddTermsToPostFromSchema($this->getTaxonomiesFactory(), $this->getTermFactory(), $wpService);

        $instance->addHooks();

        $this->assertEquals('updated_postmeta', $wpService->methodCalls['addAction'][0][0]);
    }

    #[TestDox('does nothing if metaKey is not schemaData')]
    public function testDoesNothingIfMetaKeyIsNotSchemaData(): void
    {
        $taxonomiesFactory = $this->getTaxonomiesFactory();
        $termFactory       = $this->getTermFactory();
        $wpService         = new FakeWpService();
        $instance          = new AddTermsToPostFromSchema($taxonomiesFactory, $termFactory, $wpService);

        $wpService->methodCalls = [];
        $instance->addTermsToPostFromSchema(1, 2, 'not_schemaData', 'serialized');

        $this->assertEmpty($wpService->methodCalls);
    }

    #[TestDox('does nothing if schema cannot be unserialized')]
    public function testDoesNothingIfSchemaCannotBeUnserialized(): void
    {
        $taxonomiesFactory = $this->getTaxonomiesFactory();
        $termFactory       = $this->getTermFactory();
        $wpService         = new FakeWpService();
        $instance          = new AddTermsToPostFromSchema($taxonomiesFactory, $termFactory, $wpService);

        $wpService->methodCalls = [];
        $instance->addTermsToPostFromSchema(1, 2, 'schemaData', 'not_a_serialized_string');

        $this->assertEmpty($wpService->methodCalls);
    }

    #[TestDox('does nothing if schema does not have @type')]
    public function testDoesNothingIfSchemaDoesNotHaveType(): void
    {
        $taxonomiesFactory = $this->getTaxonomiesFactory();
        $termFactory       = $this->getTermFactory();
        $wpService         = new FakeWpService();
        $instance          = new AddTermsToPostFromSchema($taxonomiesFactory, $termFactory, $wpService);

        $schema                 = serialize(['foo' => 'bar']);
        $wpService->methodCalls = [];
        $instance->addTermsToPostFromSchema(1, 2, 'schemaData', $schema);

        $this->assertEmpty($wpService->methodCalls);
    }

    #[TestDox('creates and assigns terms to post for matching taxonomies')]
    public function testCreatesAndAssignsTermsToPostForMatchingTaxonomies(): void
    {
        $taxonomyMock = $this->createMock(TaxonomyInterface::class);
        $taxonomyMock->method('getSchemaType')->willReturn('SomeType');
        $taxonomyMock->method('getName')->willReturn('custom_tax');

        $taxonomiesFactory = $this->getTaxonomiesFactory();
        $taxonomiesFactory->method('create')->willReturn([$taxonomyMock]);

        $term = (object)[
            'name'     => 'TermName',
            'taxonomy' => 'custom_tax'
        ];

        $termFactory = $this->getTermFactory();
        $termFactory->method('create')->willReturn([$term]);

        $wpService = new FakeWpService([
            'termExists'     => false,
            'wpInsertTerm'   => [],
            'wpSetPostTerms' => []
        ]);

        $instance = new AddTermsToPostFromSchema($taxonomiesFactory, $termFactory, $wpService);

        $schema = serialize(['@type' => 'SomeType']);
        $instance->addTermsToPostFromSchema(1, 42, 'schemaData', $schema);

        $this->assertEquals('TermName', $wpService->methodCalls['wpInsertTerm'][0][0]);
        $this->assertEquals('custom_tax', $wpService->methodCalls['wpInsertTerm'][0][1]);
        $this->assertEquals(42, $wpService->methodCalls['wpSetPostTerms'][0][0]);
        $this->assertEquals('TermName', $wpService->methodCalls['wpSetPostTerms'][0][1]);
        $this->assertEquals('custom_tax', $wpService->methodCalls['wpSetPostTerms'][0][2]);
    }

    #[TestDox('does not insert term if it already exists')]
    public function testDoesNotInsertTermIfItAlreadyExists(): void
    {
        $taxonomyMock = $this->createMock(TaxonomyInterface::class);
        $taxonomyMock->method('getSchemaType')->willReturn('SomeType');
        $taxonomyMock->method('getName')->willReturn('custom_tax');

        $taxonomiesFactory = $this->getTaxonomiesFactory();
        $taxonomiesFactory->method('create')->willReturn([$taxonomyMock]);

        $term = (object)[
            'name'     => 'ExistingTerm',
            'taxonomy' => 'custom_tax'
        ];

        $termFactory = $this->getTermFactory();
        $termFactory->method('create')->willReturn([$term]);

        $wpService = new FakeWpService([
            'termExists'     => true,
            'wpSetPostTerms' => []
        ]);

        $instance = new AddTermsToPostFromSchema($taxonomiesFactory, $termFactory, $wpService);

        $schema = serialize(['@type' => 'SomeType']);
        $instance->addTermsToPostFromSchema(1, 99, 'schemaData', $schema);

        $this->assertArrayNotHasKey('wpInsertTerm', $wpService->methodCalls);
        $this->assertEquals(99, $wpService->methodCalls['wpSetPostTerms'][0][0]);
        $this->assertEquals('ExistingTerm', $wpService->methodCalls['wpSetPostTerms'][0][1]);
        $this->assertEquals('custom_tax', $wpService->methodCalls['wpSetPostTerms'][0][2]);
    }

    private function getTaxonomiesFactory(): TaxonomiesFactoryInterface|MockObject
    {
        return $this->createMock(TaxonomiesFactoryInterface::class);
    }

    private function getTermFactory(): TermFactoryInterface|MockObject
    {
        return $this->createMock(TermFactoryInterface::class);
    }
}
