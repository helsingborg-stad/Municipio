<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\GetPosts;

/**
 * Decorates WP_Post with ID to indicate that this post is to be updated and is not a new post.
 */
class IdDecorator implements WpPostFactoryInterface
{
    public function __construct(private WpPostFactoryInterface $inner, private GetPosts $wpService)
    {
    }

    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post = $this->inner->create($schemaObject, $source);

        if (!empty($schemaObject['@id'])) {
            $postWithSameOriginId = $this->wpService->getPosts([
                'post_type'      => $source->getPostType(),
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_query'     => [
                    'meta_compare' => 'AND',
                    [
                        'key'     => 'originId',
                        'value'   => $schemaObject['@id'],
                        'compare' => '='
                    ],
                    [
                        'key'     => 'sourceId',
                        'value'   => $source->getId(),
                        'compare' => '='
                    ]
                ]

            ]);

            if (!empty($postWithSameOriginId)) {
                $post['ID'] = $postWithSameOriginId[0];
            }
        }

        return $post;
    }
}
