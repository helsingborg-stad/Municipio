<?php

namespace Municipio\PostsList\GetPosts;

use WP_Query;
use WpService\Contracts\GetPosts;

/**
 * Get posts using WP_Query
 */
class GetPostsUsingWpQuery implements GetPosts
{
    /**
     * Constructor
     */
    public function __construct(private WP_Query $wpQuery)
    {
    }

    /**
     * @inheritDoc
     */
    public function getPosts(?array $args = null): array
    {
        if (is_array($this->wpQuery->posts)) {
            return $this->wpQuery->posts;
        } else {
            return $this->wpQuery->get_posts();
        }
    }
}
