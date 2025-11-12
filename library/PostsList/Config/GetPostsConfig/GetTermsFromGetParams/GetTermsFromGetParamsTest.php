<?php

namespace Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WpService\Contracts\GetTerms;

class TestFilterConfig extends DefaultFilterConfig
{
    public function __construct(private array $taxonomiesEnabledForFiltering)
    {
    }

    public function getTaxonomiesEnabledForFiltering(): array
    {
        return $this->taxonomiesEnabledForFiltering;
    }
}

class TestGetTermsService implements GetTerms
{
    public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
    {
        if (
            isset($args['taxonomy'], $args['slug']) &&
            $args['taxonomy'] === 'category' &&
            in_array('term1', (array)$args['slug']) &&
            in_array('term2', (array)$args['slug'])
        ) {
            $term1       = new \WP_Term([]);
            $term1->slug = 'term1';
            $term2       = new \WP_Term([]);
            $term2->slug = 'term2';
            return [$term1, $term2];
        }
        return [];
    }
}

class GetTermsFromGetParamsTest extends TestCase
{
    #[TestDox('returns an array of terms found in the query parameters')]
    public function testGetTerms(): void
    {
        $prefix       = 'prefix_';
        $getParams    = [$prefix . 'category' => ['term1', 'term2']];
        $taxonomies   = [new TaxonomyFilterConfig('category', TaxonomyFilterType::SINGLESELECT)];
        $filterConfig = new TestFilterConfig($taxonomies);
        $wpService    = new TestGetTermsService();

        $getTermsFromGetParams = new GetTermsFromGetParams($getParams, $filterConfig, $prefix, $wpService);
        $terms                 = $getTermsFromGetParams->getTerms();

        $this->assertCount(2, $terms);
        $this->assertSame('term1', $terms[0]->slug);
        $this->assertSame('term2', $terms[1]->slug);
    }
}
