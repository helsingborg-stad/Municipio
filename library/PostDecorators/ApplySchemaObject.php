<?php

namespace Municipio\PostDecorators;

use WP_Post;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;

/**
 * ApplySchemaObject
 *
 * This class is responsible for applying a schema object to a post.
 * It uses the SchemaObjectFromPostInterface to create the schema object.
 *
 * @deprecated Use the SchemaObjectFromPostInterface::getSchemaProperty() directly instead.
 */
class ApplySchemaObject implements PostDecorator
{
    /**
     * Constructor.
     */
    public function __construct(
        private SchemaObjectFromPostInterface $schemaObjectFromPost,
        private ?PostDecorator $inner = new NullDecorator()
    ) {
        trigger_error('ApplySchemaObject is deprecated. Use the SchemaObjectFromPostInterface::getSchemaProperty() directly instead.', E_USER_DEPRECATED);
    }

    /**
     * @inheritDoc
     */
    public function apply(WP_Post $post): WP_Post
    {
        $post->schemaObject = $this->schemaObjectFromPost->create($post);
        return $post;
    }
}
