<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Taxonomy;
use WP_Term;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetTerms;

class GetTaxonomyFiltersSelectComponentArgumentsTest extends TestCase
{
    #[TestDox('returns an array of select component arguments')]
    public function testReturnsArrayOfSelectComponentArguments(): void
    {
        $taxonomy                                   = new \WP_Taxonomy([], 'category');
        $taxonomy->name                             = 'category';
        $taxonomy->label                            = 'Category';
        $filterConfig                               = $this->createFilterConfig([new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::MULTISELECT)]);
        $wpService                                  = $this->createWpService($this->getArrayOfTerms(2));
        $getTaxonomyFiltersSelectComponentArguments = new GetTaxonomyFiltersSelectComponentArguments(
            $filterConfig,
            $this->createGetPostsConfig(),
            $wpService,
            'queryVarNamePrefix_'
        );

        $this->assertEquals([
            [
                'label'       => 'Category',
                'name'        => 'queryVarNamePrefix_category',
                'required'    => false,
                'placeholder' => 'Category',
                'multiple'    => true,
                'options'     => [
                    'term-1' => 'Term 1 (1)',
                    'term-2' => 'Term 2 (1)',
                ]
            ]
        ], $getTaxonomyFiltersSelectComponentArguments->getCallable()());
    }

    #[TestDox('sets "preselected" with terms from GetPostsConfig')]
    public function testSetsPreselectedWithTermsFromGetPostsConfig(): void
    {
        $taxonomy                                   = new \WP_Taxonomy([], 'category');
        $taxonomy->name                             = 'category';
        $taxonomy->label                            = 'Category';
        $filterConfig                               = $this->createFilterConfig([new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::MULTISELECT)]);
        $wpService                                  = $this->createWpService($this->getArrayOfTerms(2));
        $preselectedTerms                           = [$this->getArrayOfTerms(1)[0]];
        $getTaxonomyFiltersSelectComponentArguments = new GetTaxonomyFiltersSelectComponentArguments(
            $filterConfig,
            $this->createGetPostsConfig($preselectedTerms),
            $wpService,
            'queryVarNamePrefix_'
        );

        $this->assertEquals([
            [
                'label'       => 'Category',
                'name'        => 'queryVarNamePrefix_category',
                'required'    => false,
                'placeholder' => 'Category',
                'preselected' => ['term-1'],
                'multiple'    => true,
                'options'     => [
                    'term-1' => 'Term 1 (1)',
                    'term-2' => 'Term 2 (1)',
                ]
            ]
        ], $getTaxonomyFiltersSelectComponentArguments->getCallable()());
    }

    #[TestDox('returns checkbox arguments for taxonomies using the checkbox filter type')]
    public function testReturnsCheckboxArgumentsForCheckboxFilterType(): void
    {
        $taxonomy        = $this->createTaxonomy('category', 'Category');
        $filterConfig    = $this->createFilterConfig([new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::CHECKBOX)]);
        $componentArgs   = new GetTaxonomyFiltersSelectComponentArguments(
            $filterConfig,
            $this->createGetPostsConfig([$this->getArrayOfTerms(1)[0]]),
            $this->createWpService($this->getArrayOfTerms(2)),
            'queryVarNamePrefix_'
        );

        $this->assertEquals([
            [
                'label'      => 'Category',
                'checkboxes' => [
                    ['type' => 'checkbox', 'label' => 'Term 1 (1)', 'name' => 'queryVarNamePrefix_category[]', 'value' => 'term-1', 'checked' => true],
                    ['type' => 'checkbox', 'label' => 'Term 2 (1)', 'name' => 'queryVarNamePrefix_category[]', 'value' => 'term-2', 'checked' => false],
                ],
            ]
        ], $componentArgs->getCallable()());
    }

    private function getArrayOfTerms(int $numberOfTerms = 1, string $taxonomy = 'category'): array
    {
        $terms = [];
        for ($i = 1; $i <= $numberOfTerms; $i++) {
            $term           = new WP_Term([]);
            $term->term_id  = $i;
            $term->name     = "Term $i";
            $term->count    = 1;
            $term->slug     = "term-$i";
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

    private function createFilterConfig(array $taxonomies): DefaultFilterConfig
    {
        return new class ($taxonomies) extends DefaultFilterConfig {
            public function __construct(private array $taxonomies)
            {
            }
            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomies;
            }
        };
    }

    private function createWpService(array $terms): GetTerms&ApplyFilters
    {
        return new class ($terms) implements GetTerms, ApplyFilters {
            public function __construct(private array $terms)
            {
            }
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                return $this->terms;
            }
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }

    private function createTaxonomy(string $name, string $label): WP_Taxonomy
    {
        $taxonomy        = new WP_Taxonomy($name, 'post');
        $taxonomy->label = $label;
        $taxonomy->name  = $name;
        return $taxonomy;
    }
}
