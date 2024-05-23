<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use Spatie\SchemaOrg\BaseType;
use WP_Post;
use WpService\Contracts\SanitizeTitle;

class ApplyPostNameFromTitle implements ApplySchemaObjectPropertiesToWpPost
{
    public function __construct(private ApplySchemaObjectPropertiesToWpPost $inner, private SanitizeTitle $wpService)
    {
    }

    public function apply(WP_Post $post, BaseType $schemaObject): WP_Post
    {
        $post = $this->inner->apply($post, $schemaObject);
        $post->post_name = $this->wpService->sanitizeTitle($post->post_title);

        return $post;
    }
}
