<?php

namespace Municipio\PostsList\GetPosts;

use WP_Query;
use WpService\Contracts\GetPosts;

class GetPostsUsingWpQuery implements GetPosts
{
    public function __construct(private WP_Query $wpQuery)
    {
    }

    public function getPosts(?array $args = null): array
    {
        if (is_array($this->wpQuery->posts)) {
            return $this->wpQuery->posts;
        } else {
            return $this->wpQuery->get_posts();
        }
    }
}
