<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use WP_Post;
use WpService\Contracts\GetThePostThumbnailUrl;

/**
 * Class SchemaObjectWithImageFromFeaturedImage
 *
 * @package Municipio\SchemaData\SchemaObjectFromPost
 */
class SchemaObjectWithImageFromFeaturedImage implements SchemaObjectFromPostInterface
{
    /**
     * SchemaObjectWithImageFromFeaturedImage constructor.
     *
     * @param SchemaObjectFromPostInterface $inner
     * @param GetThePostThumbnailUrl $wpService
     */
    public function __construct(
        private SchemaObjectFromPostInterface $inner,
        private GetThePostThumbnailUrl $wpService
    ) {
    }

    /**
     * Create a schema object from a post.
     *
     * @param WP_Post $post
     *
     * @return BaseType
     */
    public function create(WP_Post|PostObjectInterface $post): BaseType
    {
        $schema          = $this->inner->create($post);
        $schema['image'] = $this->wpService->getThePostThumbnailUrl($post->ID, 'full') ?: null;

        return $schema;
    }
}
