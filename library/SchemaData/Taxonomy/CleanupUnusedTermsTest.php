<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactoryInterface;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Term;
use WpService\Contracts\GetTerms;
use WpService\Contracts\WpDeleteTerm;
use WpService\Implementations\FakeWpService;

class CleanupUnusedTermsTest extends TestCase
{
    public function testCleanupUnusedTermsDeletesOnlyUnusedTerms()
    {
        $taxonomyMock = $this->getTaxonomy();
        $taxonomyMock->method('getName')->willReturn('category');

        $taxonomiesFactory = $this->getTaxonomiesFactory();
        $taxonomiesFactory->method('create')->willReturn([$taxonomyMock]);

        $usedTerm           = new WP_Term([]);
        $usedTerm->term_id  = 1;
        $usedTerm->taxonomy = 'category';
        $usedTerm->count    = 2;

        $unusedTerm           = new WP_Term([]);
        $unusedTerm->term_id  = 2;
        $unusedTerm->taxonomy = 'category';
        $unusedTerm->count    = 0;

        $wpService = new FakeWpService([
            'getTerms'     => fn() => [$usedTerm, $unusedTerm],
            'wpDeleteTerm' => true
        ]);

        $cleanup = new CleanupUnusedTerms($taxonomiesFactory, $wpService);
        $cleanup->cleanupUnusedTerms();

        $this->assertCount(1, $wpService->methodCalls['wpDeleteTerm']);
        $this->assertEquals(2, $wpService->methodCalls['wpDeleteTerm'][0][0]); // Check that the unused term was deleted
        $this->assertEquals('category', $wpService->methodCalls['wpDeleteTerm'][0][1]); // Check
    }

    private function getTaxonomy(): TaxonomyInterface|MockObject
    {
        return $this->createMock(TaxonomyInterface::class);
    }

    private function getTaxonomiesFactory(): TaxonomiesFactoryInterface|MockObject
    {
        return $this->createMock(TaxonomiesFromSchemaType\TaxonomiesFactoryInterface::class);
    }
}
