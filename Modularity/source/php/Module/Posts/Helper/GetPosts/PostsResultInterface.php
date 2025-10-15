<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

use WP_Post;

interface PostsResultInterface
{
    /**
     * Get posts
     * 
     * return WP_Post[]
     */
    public function getPosts():array;

    /**
     * Get number of pages for posts.
     *
     * @return int
     */
    public function getNumberOfPages():int;

    /**
     * Get posts by user ID.
     *
     * @return WP_Post[]
     */
    public function getStickyPosts():array;
}