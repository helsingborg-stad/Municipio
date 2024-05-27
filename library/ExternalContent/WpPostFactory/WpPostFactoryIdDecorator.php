<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Modularity\Module\Posts\Helper\GetPosts;
use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Post;

/**
 * Decorates WP_Post with ID to indicate that this post is to be updated and is not a new post.
 */
class WpPostFactoryIdDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner, private GetPosts $wpService)
    {
    }

    public function create(BaseType $schemaObject, ISource $source): WP_Post
    {
        $post = $this->inner->create($schemaObject, $source);

        if (!empty($schemaObject['@id'])) {
            $postWithSameOriginId = $this->wpService->getPosts([
                'post_type'      => 'any',
                'posts_per_page' => 1,
                'meta_key'       => 'originId',
                'meta_value'     => $schemaObject['@id'],
            ]);

            if (!empty($postWithSameOriginId)) {
                $post->ID = $postWithSameOriginId[0]->ID;
            }
        }

        return $post;
    }
}
