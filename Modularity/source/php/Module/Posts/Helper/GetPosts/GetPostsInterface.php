<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

interface GetPostsInterface
{
    /**
     * Get posts
     * 
     * @return PostsResultInterface
     */
    public function getPosts(): PostsResultInterface;
}