<?php

namespace Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig;

interface TaxonomyFilterConfigInterface
{
    /**
     * Get taxonomy name
     *
     * @return string
     */
    public function getTaxonomyName(): string;

    /**
     * Get filter type
     *
     * @return TaxonomyFilterType
     */
    public function getFilterType(): TaxonomyFilterType;
}
