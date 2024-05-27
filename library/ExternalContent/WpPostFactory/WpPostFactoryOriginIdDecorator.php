<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

class WpPostFactoryOriginIdDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner)
    {
    }

    public function create(BaseType $schemaObject): WP_Post
    {
        /** @var WP_Post $post */
        $post = $this->inner->create($schemaObject);

        $post->post_date     = $schemaObject['datePublished'] ?? null;
        $post->post_modified = $schemaObject['dateModified'] ?? null;

        return $post;
    }
}
