<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

class AddMetaPropertyWithSchemaData implements ApplySchemaObjectPropertiesToWpPost
{
    public function __construct(private ApplySchemaObjectPropertiesToWpPost $inner)
    {
    }

    public function apply(WP_Post $post, BaseType $schemaObject): WP_Post
    {
        $post       = $this->inner->apply($post, $schemaObject);
        $post->meta = ['schema' => $schemaObject->toArray()];

        return $post;
    }
}
