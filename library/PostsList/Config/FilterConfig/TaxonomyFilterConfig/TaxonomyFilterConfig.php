<?php

namespace Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig;

/**
 * class TaxonomyFilterConfig
 */
class TaxonomyFilterConfig implements TaxonomyFilterConfigInterface
{
    /**
     * Constructor
     *
     * @param \WP_Taxonomy $taxonomy
     * @param TaxonomyFilterType $filterType
     */
    public function __construct(private \WP_Taxonomy $taxonomy, private TaxonomyFilterType $filterType)
    {
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomy(): \WP_Taxonomy
    {
        return $this->taxonomy;
    }

    /**
     * @inheritDoc
     */
    public function getFilterType(): TaxonomyFilterType
    {
        return $this->filterType;
    }
}
