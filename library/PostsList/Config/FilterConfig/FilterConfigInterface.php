<?php

namespace Municipio\PostsList\Config\FilterConfig;

use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfigInterface;

interface FilterConfigInterface
{
    /**
     * Is text search enabled
     *
     * @return bool
     */
    public function isTextSearchEnabled(): bool;

    /**
     * Is date filter enabled
     *
     * @return bool
     */
    public function isDateFilterEnabled(): bool;

    /**
     * Get taxonomies enabled for filtering
     *
     * @return TaxonomyFilterConfigInterface[]
     */
    public function getTaxonomiesEnabledForFiltering(): array;

    /**
     * Show filter reset button
     *
     * @return bool
     */
    public function showReset(): bool;

    /**
     * Get reset URL
     *
     * @return string|null
     */
    public function getResetUrl(): null|string;
}
