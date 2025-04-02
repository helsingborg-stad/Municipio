<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;
use WpService\Contracts\GetPosts;

/**
 * Decorates WP_Post with ID to indicate that this post is to be updated and is not a new post.
 */
class IdDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * Class constructor.
     *
     * @param string $postType
     * @param string $sourceId
     * @param WpPostArgsFromSchemaObjectInterface $inner
     * @param GetPosts $wpService
     */
    public function __construct(
        private string $postType,
        private string $sourceId,
        private WpPostArgsFromSchemaObjectInterface $inner,
        private GetPosts $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $post = $this->inner->transform($schemaObject);

        if (!empty($schemaObject['@id'])) {
            $postWithSameOriginId = $this->wpService->getPosts([
                'post_type'      => $this->postType,
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
                        'value'   => $this->sourceId,
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
