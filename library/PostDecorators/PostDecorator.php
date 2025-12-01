<?php

namespace Municipio\PostDecorators;

interface PostDecorator
{
    public function apply(\WP_Post $post): \WP_Post;
}
