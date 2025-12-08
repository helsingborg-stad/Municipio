<?php

namespace Municipio\PostObject\Factory;

use Municipio\PostObject\PostObjectInterface;

interface PostObjectFromWpPostFactoryInterface
{
    /**
     * Create a PostObject from a WP_Post object.
     *
     * @param \WP_Post $post
     * @return PostObjectInterface
     */
    public function create(\WP_Post $post): PostObjectInterface;
}
