<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Term;

class ApplyTaxQueryTest extends TestCase
{
    #[TestDox('Applies tax query from config to args')]
    public function testApplyAddsTaxQueryToArgs(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getTerms(): array
            {
                $categoryTerm           = new WP_Term([]);
                $categoryTerm->term_id  = 1;
                $categoryTerm->taxonomy = 'category';

                $tagTerm           = new WP_Term([]);
                $tagTerm->term_id  = 2;
                $tagTerm->taxonomy = 'post_tag';

                return [$categoryTerm, $tagTerm];
            }
        };

        $applier = new ApplyTaxQuery();
        $args    = $applier->apply($config, []);

        $this->assertContains([
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => [1],
        ], $args['tax_query']);

        $this->assertContains([
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => [2],
        ], $args['tax_query']);
    }

    #[TestDox('relation is OR by default')]
    public function testApplyAddsDefaultRelationToArgs(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getTerms(): array
            {
                $categoryTerm           = new WP_Term([]);
                $categoryTerm->term_id  = 1;
                $categoryTerm->taxonomy = 'category';

                $tagTerm           = new WP_Term([]);
                $tagTerm->term_id  = 2;
                $tagTerm->taxonomy = 'post_tag';

                return [$categoryTerm, $tagTerm];
            }
        };

        $applier = new ApplyTaxQuery();
        $args    = $applier->apply($config, []);

        $this->assertEquals('OR', $args['tax_query']['relation']);
    }

    #[TestDox('relation is AND if config defines query as faceting')]
    public function testApplyAddsAndRelationToArgsWhenFaceting(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function isFacettingTaxonomyQueryEnabled(): bool
            {
                return true;
            }

            public function getTerms(): array
            {
                $categoryTerm           = new WP_Term([]);
                $categoryTerm->term_id  = 1;
                $categoryTerm->taxonomy = 'category';

                $tagTerm           = new WP_Term([]);
                $tagTerm->term_id  = 2;
                $tagTerm->taxonomy = 'post_tag';

                return [$categoryTerm, $tagTerm];
            }
        };

        $applier = new ApplyTaxQuery();
        $args    = $applier->apply($config, []);

        $this->assertEquals('AND', $args['tax_query']['relation']);
    }

    #[TestDox('Does not modify args if no terms in config')]
    public function testApplyWithoutTermsDoesNotModifyArgs(): void
    {
        $config = new DefaultGetPostsConfig();

        $applier = new ApplyTaxQuery();
        $args    = $applier->apply($config, []);

        $this->assertEquals([], $args);
    }
}
