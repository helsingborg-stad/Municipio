<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

class ApplyDefaultProperties implements ApplySchemaObjectPropertiesToWpPost
{
    public function apply(WP_Post $post, BaseType $schemaObject): WP_Post
    {
        $post->ID           = $schemaObject['@id'];
        $post->post_title   = $schemaObject['name'];
        $post->post_content = $schemaObject['description'];
        $post->post_excerpt = $schemaObject['excerpt'];
        $post->post_status  = 'publish';

        return $post;
    }
}
