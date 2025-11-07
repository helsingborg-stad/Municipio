<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

interface GetPostsConfigInterface
{
    /**
     * Get post type(s) for the posts list
     *
     * @return string[] Array of post type slugs
     */
    public function getPostTypes(): array;

    /**
     * Get number of posts per page
     *
     * @return int
     */
    public function getPostsPerPage(): int;

    /**
     * Check if facetting is enabled
     *
     * @return bool
     */
    public function isFacettingTaxonomyQueryEnabled(): bool;

    /**
     * Get search query
     *
     * @return string|null
     */
    public function getSearch(): ?string;

    /**
     * Get date from filter value
     *
     * @return string|null
     */
    public function getDateFrom(): ?string;

    /**
     * Get date to filter value
     *
     * @return string|null
     */
    public function getDateTo(): ?string;
}
