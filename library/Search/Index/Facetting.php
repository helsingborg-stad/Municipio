<?php

namespace Municipio\Search\Index;

class Facetting
{
    public function __construct()
    {
        add_filter('AlgoliaIndex/Facets', array($this, 'addFacettingOptions'));
        add_filter('AlgoliaIndex/FacetingEnabled', array($this, 'isFacettingEnabled'));
    }

    /**
     * Add facetting options from settings to facets
     *
     * @param   array $existingFacets   The existing facets
     * @param   bool  $includeDisabled  Whether to include disabled facets
     * @return  array                   The merged facets
     */
    public function addFacettingOptions($existingFacets): null|array
    {
        $facets = \AlgoliaIndex\Helper\Options::facetting(true) ?? [];
        $facets = array_merge($facets, $existingFacets);
        return $facets;
    }

    /**
     * Check if facetting is enabled
     *
     * @param   bool $enabled   The current enabled state
     * @return  bool            The updated enabled state
     */
    public function isFacettingEnabled($enabled): bool
    {
        return !empty(\AlgoliaIndex\Helper\Options::facetting(false));
    }
}
