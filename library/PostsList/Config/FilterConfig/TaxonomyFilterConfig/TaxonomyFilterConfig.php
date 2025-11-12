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
     * @param string $taxonomyName
     * @param TaxonomyFilterType $filterType
     */
    public function __construct(private string $taxonomyName, private TaxonomyFilterType $filterType)
    {
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomyName(): string
    {
        return $this->taxonomyName;
    }

    /**
     * @inheritDoc
     */
    public function getFilterType(): TaxonomyFilterType
    {
        return $this->filterType;
    }
}
