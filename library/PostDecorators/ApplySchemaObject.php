<?php

namespace Municipio\PostDecorators;

use WP_Post;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;

class ApplySchemaObject implements PostDecorator
{
    public function __construct(
        private SchemaObjectFromPostInterface $schemaObjectFromPost,
        private ?PostDecorator $inner = new NullDecorator()
    ) {
    }

    public function apply(WP_Post $post): WP_Post
    {
        $post->schemaObject = $this->schemaObjectFromPost->create($post);
        return $post;
    }
}
