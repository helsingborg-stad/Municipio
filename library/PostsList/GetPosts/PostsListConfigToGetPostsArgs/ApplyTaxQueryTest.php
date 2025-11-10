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

        $this->assertEquals([
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => [1],
                ],
                [
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => [2],
                ],
            ],
        ], $args);
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
