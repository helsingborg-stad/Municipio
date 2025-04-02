<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Schema\BaseType;
use WP_Post;
use WpService\Contracts\GetThePostThumbnailUrl;

class SchemaObjectWithImageFromFeaturedImage implements SchemaObjectFromPostInterface
{
    public function __construct(
        private SchemaObjectFromPostInterface $inner,
        private GetThePostThumbnailUrl $wpService
    ) {
    }

    public function create(WP_Post $post): BaseType
    {
        $schema          = $this->inner->create($post);
        $schema['image'] = $this->wpService->getThePostThumbnailUrl($post->ID, 'full') ?: null;

        return $schema;
    }
}
