<?php

namespace Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig;

interface TaxonomyFilterConfigInterface
{
    /**
     * Get taxonomy
     *
     * @return \WP_Taxonomy
     */
    public function getTaxonomy(): \WP_Taxonomy;

    /**
     * Get filter type
     *
     * @return TaxonomyFilterType
     */
    public function getFilterType(): TaxonomyFilterType;
}
