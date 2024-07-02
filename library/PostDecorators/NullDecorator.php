<?php

namespace Municipio\PostDecorators;

use WP_Post;

class NullDecorator implements PostDecorator
{
    public function apply(WP_Post $post): WP_Post
    {
        return $post;
    }
}
