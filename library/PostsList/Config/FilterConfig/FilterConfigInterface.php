<?php

namespace Municipio\PostsList\Config\FilterConfig;

interface FilterConfigInterface
{
    /**
     * Is filter enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;

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
     * @return array
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
    public function getResetUrl(): ?string;
}
