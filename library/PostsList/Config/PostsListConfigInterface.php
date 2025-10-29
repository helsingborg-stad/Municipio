<?php

namespace Municipio\PostsList\Config;

use Municipio\PostsList\Config\PostsListAppearanceConfig\PostsListAppearanceConfigInterface;

interface PostsListConfigInterface
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
     * Get design for the posts list
     *
     * @return PostsListAppearanceConfigInterface
     */
    public function getAppearanceConfig(): PostsListAppearanceConfigInterface;
}
