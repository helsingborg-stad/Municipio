<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Post;

class WpPostFactory implements WpPostFactoryInterface
{
    public function create(BaseType $schemaObject, ISource $source): WP_Post
    {
        $post               = new WP_Post((object) []);
        $post->post_title   = $schemaObject['name'] ?? '';
        $post->post_content = $schemaObject['description'] ?? '';
        $post->post_status  = 'publish';
        $post->post_type    = $source->getPostType();

        return $post;
    }
}
