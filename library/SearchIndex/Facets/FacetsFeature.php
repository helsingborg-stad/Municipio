<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Facets;

use Municipio\SearchIndex\Config\FacetsConfig;
use WpService\WpService;

/**
 * Exposes configured facet selections to search UI integrations.
 */
class FacetsFeature
{
    public function __construct(
        private WpService $wpService,
        private FacetsConfig $config,
    ) {}

    /**
     * Register generic facet configuration filters.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/Facets', [$this, 'addConfiguredFacets']);
        $this->wpService->addFilter('Municipio/SearchIndex/FacetingEnabled', [$this, 'isFacetingEnabled']);
    }

    /**
     * Add enabled configured facets to facets supplied by an integration.
     */
    public function addConfiguredFacets(array $facets): array
    {
        return [...$this->config->getFacets(true), ...$facets];
    }

    /**
     * Determine if one or more facets have been configured.
     */
    public function isFacetingEnabled(bool $enabled): bool
    {
        return $enabled || $this->config->getFacets(true) !== [];
    }
}