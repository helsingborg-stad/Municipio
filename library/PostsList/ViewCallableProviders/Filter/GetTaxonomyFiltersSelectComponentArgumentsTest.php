<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Taxonomy;
use WP_Term;
use WpService\Contracts\GetTerms;

class GetTaxonomyFiltersSelectComponentArgumentsTest extends TestCase
{
    #[TestDox('returns an array of select component arguments')]
    public function testReturnsArrayOfSelectComponentArguments(): void
    {
        $expected                    = [
            [
                'label'       => 'Category',
                'name'        => 'category',
                'required'    => false,
                'placeholder' => 'Category',
                'multiple'    => true,
                'options'     => [
                    'term-1' => 'Term 1 (1)',
                    'term-2' => 'Term 2 (1)',
                ]
            ]
        ];
        $registeredWpTaxonomy        = new WP_Taxonomy('category', 'post');
        $registeredWpTaxonomy->label = 'Category';
        $registeredWpTaxonomy->name  = 'category';

        $filterConfig = new class extends DefaultFilterConfig {
            public function getTaxonomiesEnabledForFiltering(): array
            {
                return ['category'];
            }
        };

        $wpService = new class ($this->getArrayOfTerms(2)) implements GetTerms {
            public function __construct(private array $terms)
            {
            }
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                return $this->terms;
            }
        };

        $getTaxonomyFiltersSelectComponentArguments = new GetTaxonomyFiltersSelectComponentArguments($filterConfig, $this->createGetPostsConfig(), $wpService, ['category' => $registeredWpTaxonomy]);
        $callable                                   = $getTaxonomyFiltersSelectComponentArguments->getCallable();

        $this->assertEquals($expected, $callable());
    }

    #[TestDox('sets "preselected" with terms from GetPostsConfig')]
    public function testSetsPreselectedWithTermsFromGetPostsConfig(): void
    {
        $expected                    = [
            [
                'label'       => 'Category',
                'name'        => 'category',
                'required'    => false,
                'placeholder' => 'Category',
                'preselected' => [ 'term-1', ],
                'multiple'    => true,
                'options'     => [
                    'term-1' => 'Term 1 (1)',
                    'term-2' => 'Term 2 (1)',
                ]
            ]
        ];
        $registeredWpTaxonomy        = new WP_Taxonomy('category', 'post');
        $registeredWpTaxonomy->label = 'Category';
        $registeredWpTaxonomy->name  = 'category';

        $filterConfig = new class extends DefaultFilterConfig {
            public function getTaxonomiesEnabledForFiltering(): array
            {
                return ['category'];
            }
        };

        $wpService = new class ($this->getArrayOfTerms(2)) implements GetTerms {
            public function __construct(private array $terms)
            {
            }
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                return $this->terms;
            }
        };

        $getTaxonomyFiltersSelectComponentArguments = new GetTaxonomyFiltersSelectComponentArguments(
            $filterConfig,
            $this->createGetPostsConfig([$this->getArrayOfTerms(1)[0]]),
            $wpService,
            ['category' => $registeredWpTaxonomy]
        );
        $callable                                   = $getTaxonomyFiltersSelectComponentArguments->getCallable();

        $this->assertEquals($expected, $callable());
    }

    private function getArrayOfTerms(int $numberOfTerms = 1, string $taxonomy = 'category'): array
    {
        $terms = [];
        for ($i = 1; $i <= $numberOfTerms; $i++) {
            $term           = new WP_Term([]);
            $term->term_id  = $i;
            $term->name     = 'Term ' . $i;
            $term->count    = 1;
            $term->slug     = 'term-' . $i;
            $term->taxonomy = $taxonomy;
            $terms[]        = $term;
        }
        return $terms;
    }

    private function createGetPostsConfig(array $termsUsedForFiltering = []): GetPostsConfigInterface
    {
        return new class ($termsUsedForFiltering) extends DefaultGetPostsConfig {
            public function __construct(private array $termsUsedForFiltering)
            {
            }

            public function getTerms(): array
            {
                return $this->termsUsedForFiltering;
            }
        };
    }
}
