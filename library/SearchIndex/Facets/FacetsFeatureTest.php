<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Facets;

use AcfService\Implementations\FakeAcfService;
use Municipio\SearchIndex\Config\FacetsConfig;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests configured facet exposure for search UI integrations.
 */
class FacetsFeatureTest extends TestCase
{
    /**
     * Verify that only enabled, valid facets are exposed to integrations.
     */
    public function testAddsEnabledConfiguredFacets(): void
    {
        $feature = new FacetsFeature(new FakeWpService(), new FacetsConfig(new FakeAcfService([
            'getField' => [[
                'attribute' => 'categories',
                'label' => 'Categories',
                'enabled' => true,
            ], [
                'attribute' => 'tags',
                'label' => 'Tags',
                'enabled' => false,
            ], [
                'attribute' => '',
                'label' => 'Invalid',
                'enabled' => true,
            ]],
        ])));

        static::assertSame([[
            'attribute' => 'categories',
            'label' => 'Categories',
            'enabled' => true,
        ]], $feature->addConfiguredFacets([]));
        static::assertTrue($feature->isFacetingEnabled(false));
    }
}