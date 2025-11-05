<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Term;
use WpService\Contracts\GetTerms;

class GetTaxonomyFiltersSelectComponentArgumentsTest extends TestCase
{
    #[TestDox('returns an array of select component arguments')]
    public function testReturnsArrayOfSelectComponentArguments(): void
    {
        $expected = [
            [
                'label'       => 'Category',
                'name'        => 'filter-category',
                'required'    => false,
                'placeholder' => 'Category',
                'preselected' => false,
                'multiple'    => true,
                'options'     => [
                    'term-1' => 'Term 1 (1)',
                    'term-2' => 'Term 2 (1)',
                ]
            ]
        ];

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

        $getTaxonomyFiltersSelectComponentArguments = new GetTaxonomyFiltersSelectComponentArguments($filterConfig, $wpService);
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
}
