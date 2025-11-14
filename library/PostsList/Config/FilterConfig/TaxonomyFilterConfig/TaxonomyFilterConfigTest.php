<?php

namespace Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TaxonomyFilterConfigTest extends TestCase
{
    #[TestDox('returns provided taxonomy')]
    public function testReturnsProvidedTaxonomy(): void
    {
        $taxonomy = new \WP_Taxonomy([], 'category');
        $config   = new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::SINGLESELECT);
        $this->assertSame($taxonomy, $config->getTaxonomy());
    }

    #[TestDox('returns provided filter type')]
    public function testReturnsProvidedFilterType(): void
    {
        $taxonomy = new \WP_Taxonomy([], 'category');
        $config   = new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::MULTISELECT);
        $this->assertSame(TaxonomyFilterType::MULTISELECT, $config->getFilterType());
    }
}
