<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;


use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
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
                $categoryTerm = new WP_Term([]);
                $categoryTerm->term_id = 1;
                $categoryTerm->taxonomy = 'category';

                $tagTerm = new WP_Term([]);
                $tagTerm->term_id = 2;
                $tagTerm->taxonomy = 'post_tag';

                return [$categoryTerm, $tagTerm];
            }
        };

        $categoryTaxonomy = new \WP_Taxonomy([], 'category');
        $categoryTaxonomy->name = 'category';
        $categoryTaxonomy->hierarchical = true;
        $tagTaxonomy = new \WP_Taxonomy([], 'post_tag');
        $tagTaxonomy->name = 'post_tag';
        $tagTaxonomy->hierarchical = false;

        $taxonomyFilterConfigs = [
            new TaxonomyFilterConfig($categoryTaxonomy, TaxonomyFilterType::SINGLESELECT),
            new TaxonomyFilterConfig($tagTaxonomy, TaxonomyFilterType::SINGLESELECT),
        ];

        $applier = new ApplyTaxQuery($taxonomyFilterConfigs);
        $args = $applier->apply($config, []);

        $this->assertContains(
            [
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => [1],
                'operator' => 'IN',
            ],
            $args['tax_query'],
        );

        $this->assertContains(
            [
                'taxonomy' => 'post_tag',
                'field' => 'term_id',
                'terms' => [2],
                'operator' => 'IN',
            ],
            $args['tax_query'],
        );
    }

    #[TestDox('relation is OR by default')]
    public function testApplyAddsDefaultRelationToArgs(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getTerms(): array
            {
                $categoryTerm = new WP_Term([]);
                $categoryTerm->term_id = 1;
                $categoryTerm->taxonomy = 'category';

                $tagTerm = new WP_Term([]);
                $tagTerm->term_id = 2;
                $tagTerm->taxonomy = 'post_tag';

                return [$categoryTerm, $tagTerm];
            }
        };

        $taxonomy = new \WP_Taxonomy([], 'category');
        $taxonomy->name = 'category';
        $taxonomy->hierarchical = true;
        $taxonomyFilterConfigs = [new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::SINGLESELECT)];

        $applier = new ApplyTaxQuery($taxonomyFilterConfigs);
        $args = $applier->apply($config, []);

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
                $categoryTerm = new WP_Term([]);
                $categoryTerm->term_id = 1;
                $categoryTerm->taxonomy = 'category';

                $tagTerm = new WP_Term([]);
                $tagTerm->term_id = 2;
                $tagTerm->taxonomy = 'post_tag';

                return [$categoryTerm, $tagTerm];
            }
        };

        $taxonomy = new \WP_Taxonomy([], 'category');
        $taxonomy->name = 'category';
        $taxonomy->hierarchical = true;
        $taxonomyFilterConfigs = [new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::SINGLESELECT)];

        $applier = new ApplyTaxQuery($taxonomyFilterConfigs);
        $args = $applier->apply($config, []);

        $this->assertEquals('AND', $args['tax_query']['relation']);
    }

    #[TestDox('Does not modify args if no terms in config')]
    public function testApplyWithoutTermsDoesNotModifyArgs(): void
    {
        $config = new DefaultGetPostsConfig();

        $applier = new ApplyTaxQuery([]);
        $args = $applier->apply($config, []);

        $this->assertEquals([], $args);
    }
}
