<?php

namespace Municipio\PostsList;

use Municipio\PostsList\ConfigMapper\PostsListConfigDTOInterface;

interface PostsListFactoryInterface
{
    /**
     * Create a PostsList instance
     */
    public function create(PostsListConfigDTOInterface $postsListConfigDTO): PostsListInterface;
}
