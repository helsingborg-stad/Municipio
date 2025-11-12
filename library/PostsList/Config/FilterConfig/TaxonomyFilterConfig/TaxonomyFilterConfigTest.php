<?php

namespace Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TaxonomyFilterConfigTest extends TestCase
{
    #[TestDox('returns provided taxonomy name')]
    public function testReturnsProvidedTaxonomyName(): void
    {
        $config = new TaxonomyFilterConfig('category', TaxonomyFilterType::SINGLESELECT);
        $this->assertSame('category', $config->getTaxonomyName());
    }

    #[TestDox('returns provided filter type')]
    public function testReturnsProvidedFilterType(): void
    {
        $config = new TaxonomyFilterConfig('category', TaxonomyFilterType::MULTISELECT);
        $this->assertSame(TaxonomyFilterType::MULTISELECT, $config->getFilterType());
    }
}
