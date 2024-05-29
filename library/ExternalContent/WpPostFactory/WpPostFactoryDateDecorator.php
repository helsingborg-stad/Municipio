<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Thing;
use WP_Post;

class WpPostFactoryDateDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner)
    {
    }

    public function create(BaseType $schemaObject, ISource $source): WP_Post
    {
        $post = $this->inner->create($schemaObject, $source);

        $post->post_date     = $schemaObject['datePublished'] ?? null;
        $post->post_modified = $schemaObject['dateModified'] ?? null;

        return $post;
    }
}
