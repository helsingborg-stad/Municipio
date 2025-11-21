<?php

declare(strict_types=1);

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
