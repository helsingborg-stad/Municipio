<?php

namespace Municipio\Schema\PostDecorator;

interface SchemaDecoratorInterface
{
    public function appendData(\WP_Post $post): \WP_Post;
}