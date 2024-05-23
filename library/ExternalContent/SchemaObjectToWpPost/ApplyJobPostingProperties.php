<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

class ApplyJobPostingProperties implements ApplySchemaObjectPropertiesToWpPost
{
    public function __construct(private ApplySchemaObjectPropertiesToWpPost $inner)
    {
    }

    public function apply(WP_Post $post, BaseType $schemaObject): WP_Post
    {
        if ($schemaObject['@type'] !== 'JobPosting') {
            return $this->inner->apply($post, $schemaObject);
        }

        $post             = $this->inner->apply($post, $schemaObject);
        $post->post_title = $schemaObject['title'];

        return $post;
    }
}
